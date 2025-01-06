<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\Dto\Request\Api\PaymentDetails\FrontendTokenComplete;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Dto\Response\App\PaymentDetails\FrontendToken;
use BillaBear\Entity\Customer;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\PaymentMethod\DefaultPaymentManagerInterface;
use Parthenon\Billing\PaymentMethod\DeleterInterface;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDetailsController
{
    use LoggerAwareTrait;

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{customerId}/payment-card/frontend-payment-token', name: 'app_payment_details_frontend_payment_token_start', methods: ['GET'])]
    public function startJsTokenAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        FrontendConfig $config,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Request to get frontend token for stripe card add', ['customer_id' => $request->get('customerId')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $defaultSettings = $settingsRepository->getDefaultSettings();
        $apiKey = empty($config->getApiInfo()) ? $defaultSettings->getSystemSettings()->getStripePublicKey() : $config->getApiInfo();
        $token = $addCardByTokenDriver->startTokenProcess($customer);
        $dto = new FrontendToken();
        $dto->setToken($token);
        $dto->setApiInfo($apiKey);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{customerId}/payment-card/frontend-payment-token', name: 'app_payment_details_frontend_payment_token_complete', methods: ['POST'])]
    public function finishFrontendAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PaymentMethodsDataMapper $paymentDetailsFactory,
    ): Response {
        $this->getLogger()->info('Finishing the frontend process for stripe card add', ['customer_id' => $request->get('customerId')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), FrontendTokenComplete::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $paymentCard = $addCardByTokenDriver->createPaymentDetailsFromToken($customer, $dto->getToken());

        $output = $paymentDetailsFactory->createApiDto($paymentCard);
        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/app/customer/{customerId}/payment-card', name: 'app_payment_details_list', methods: ['GET'])]
    public function listCustomerPaymentMethods(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentDetailsFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('List payment methods for customer', ['customer_id' => $request->get('customerId')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $paymentDetailsDtos = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);
        $listView = new ListResponse();
        $listView->setData($paymentDetailsDtos);

        $json = $serializer->serialize($listView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{customerId}/payment-card/{paymentDetailsId}/default', name: 'app_payment_details_default', methods: ['POST'])]
    public function makeDefault(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DefaultPaymentManagerInterface $defaultPaymentManager,
    ): Response {
        $this->getLogger()->info('Make a payment card default', [
            'customer_id' => $request->get('customerId'),
            'payment_details_id' => $request->get('paymentDetailsId'),
        ]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($customer, $paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/customer/{customerId}/payment-card/{paymentDetailsId}', name: 'app_payment_details_delete', methods: ['DELETE'])]
    public function deletePaymentDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DeleterInterface $deleter,
    ): Response {
        $this->getLogger()->info('Delete a payment card', [
            'customer_id' => $request->get('customerId'),
            'payment_details_id' => $request->get('paymentDetailsId'),
        ]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $deleter->delete($paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
