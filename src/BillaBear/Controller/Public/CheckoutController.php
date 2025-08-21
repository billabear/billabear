<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Checkout\CheckoutSessionCreator;
use BillaBear\Customer\ExternalRegisterInterface;
use BillaBear\DataMappers\CheckoutDataMapper;
use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\Dto\Request\Public\Checkout\PayCheckout;
use BillaBear\Dto\Request\Public\CreateCustomerDto;
use BillaBear\Dto\Response\Portal\Checkout\CheckoutSession;
use BillaBear\Dto\Response\Portal\Checkout\CustomerCreation;
use BillaBear\Dto\Response\Portal\Checkout\ViewCheckout;
use BillaBear\Dto\Response\Portal\Quote\StripeInfo;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Quotes\QuoteConverter;
use BillaBear\Repository\CheckoutRepositoryInterface;
use BillaBear\Repository\CheckoutSessionRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CheckoutController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/checkout/{slug}/view', name: 'app_public_checkout_readcheckout', methods: ['GET'])]
    public function readCheckout(
        Request $request,
        CheckoutRepositoryInterface $checkoutRepository,
        CheckoutDataMapper $checkoutDataMapper,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to read checkout', ['checkout_slug' => $request->get('slug')]);

        try {
            $checkout = $checkoutRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$checkout->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $checkoutDataMapper->createPublicDto($checkout);
        $viewDto = new ViewCheckout();
        $viewDto->setCheckout($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/checkout/{slug}/customer', name: 'app_public_checkout_createcustomercheckout', methods: ['POST'])]
    public function createCustomerCheckout(
        Request $request,
        CustomerDataMapper $customerDataMapper,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CheckoutRepositoryInterface $checkoutRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        ExternalRegisterInterface $externalRegister,
        SettingsRepositoryInterface $settingsRepository,
        FrontendConfig $config,
        CheckoutSessionCreator $checkoutSessionCreator,
    ) {
        $this->getLogger()->info('Received request to create customer checkout', ['checkout_slug' => $request->get('slug')]);

        try {
            $checkout = $checkoutRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$checkout->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var CreateCustomerDto $input */
        $input = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
        $customer = $customerDataMapper->createCustomer($input);
        $customer->setBrand($checkout->getBrandSettings()->getCode());
        $customer->setBrandSettings($checkout->getBrandSettings());
        $externalRegister->register($customer);
        $customerRepository->save($customer);

        $checkoutSession = $checkoutSessionCreator->createCheckoutSession($checkout, $customer);

        $amounts = new CheckoutSession();
        $amounts->setId((string) $checkoutSession->getId());
        $amounts->setAmountDue($checkoutSession->getAmountDue());
        $amounts->setTaxTotal($checkoutSession->getTaxTotal());
        $amounts->setSubTotal($checkoutSession->getSubTotal());
        $amounts->setCurrency($checkoutSession->getCurrency());

        $defaultSettings = $settingsRepository->getDefaultSettings();
        $apiKey = empty($config->getApiInfo()) ? $defaultSettings->getSystemSettings()->getStripePublicKey() : $config->getApiInfo();

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($customer));
        $stripe->setKey($apiKey);
        $viewDto = new CustomerCreation();
        $viewDto->setStripe($stripe);
        $viewDto->setCheckoutSession($amounts);

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/checkout/{slug}/pay', name: 'app_public_checkout_createpaymentcheckout', methods: ['POST'])]
    public function createPaymentCheckout(
        Request $request,
        CheckoutRepositoryInterface $checkoutRepository,
        CheckoutSessionRepositoryInterface $checkoutSessionRepository,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        QuoteConverter $quoteConverter,
        InvoiceCharger $invoiceCharger,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->getLogger()->info('Received request to create payment checkout', ['checkout_slug' => $request->get('slug')]);

        try {
            $checkout = $checkoutRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$checkout->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $inputDto = $serializer->deserialize($request->getContent(), PayCheckout::class, 'json');
        $checkoutSession = $checkoutSessionRepository->findById($inputDto->getCheckoutSession());

        $paymentCard = $addCardByTokenDriver->createPaymentDetailsFromToken($checkoutSession->getCustomer(), $inputDto->getToken());
        $invoice = $quoteConverter->convertToInvoice($checkoutSession);
        $checkoutSessionRepository->save($checkoutSession);

        $success = true;
        $failureReason = null;

        try {
            $invoiceCharger->chargeInvoice($invoice, $paymentCard);
            foreach ($invoice->getSubscriptions() as $subscription) {
                $eventDispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);
            }

            if (!$checkout->isPermanent()) {
                $checkout->setValid(false);
                $checkoutRepository->save($checkout);
            }
        } catch (PaymentFailureException $e) {
            $success = false;
            $failureReason = $e->getReason()->value;
        }

        return new JsonResponse(['success' => $success, 'failure_reason' => $failureReason]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
