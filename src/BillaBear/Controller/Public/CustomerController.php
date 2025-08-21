<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\InvoiceDataMapper;
use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Response\Portal\Customer\MainView;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\ManageCustomerSessionRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/customer/{token}/manage', name: 'app_public_customer_manage', methods: ['GET'])]
    public function readCheckout(
        Request $request,
        ManageCustomerSessionRepositoryInterface $manageCustomerSessionRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        PaymentCardRepositoryInterface $paymentCardRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        PaymentMethodsDataMapper $paymentMethodsDataMapper,
        SubscriptionDataMapper $subscriptionDataMapper,
        InvoiceDataMapper $invoiceDataMapper,
        CustomerDataMapper $customerDataMapper,
        SerializerInterface $serializer,
    ) {
        try {
            $session = $manageCustomerSessionRepository->getByToken($request->get('token'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->warning('Unable to find customer management session', ['token' => $request->get('token')]);

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info(
            'Received request to manage customer via portal',
            [
                'token' => $request->get('token'),
                'customer_id' => (string) $session->getCustomer()->getId(),
            ]
        );

        $now = new \DateTime();

        if ($session->getExpiresAt() < $now) {
            $this->getLogger()->warning(
                'Customer management Session has expired',
                [
                    'token' => $request->get('token'),
                    'customer_id' => (string) $session->getCustomer()->getId(),
                ]
            );

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $expiresAt = new \DateTime('+5 minutes');
        $session->setExpiresAt($expiresAt);
        $session->setUpdatedAt($now);
        $manageCustomerSessionRepository->save($session);

        $customer = $session->getCustomer();
        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);
        $invoicesSet = $invoiceRepository->getLastTenForCustomer($customer);
        $paymentCards = $paymentCardRepository->getPaymentCardForCustomer($customer);

        $customerDto = $customerDataMapper->createPublicDto($customer);
        $subscriptionDtos = array_map([$subscriptionDataMapper, 'createPublicDto'], $subscriptions);
        $invoiceDtos = array_map([$invoiceDataMapper, 'createPublicDto'], $invoicesSet->getResults());
        $paymentCardDtos = array_map([$paymentMethodsDataMapper, 'createPublicDto'], $paymentCards);

        $mainView = new MainView(
            $customerDto,
            $subscriptionDtos,
            $paymentCardDtos,
            $invoiceDtos,
        );
        $json = $serializer->serialize($mainView, 'json');

        return JsonResponse::fromJsonString($json);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
