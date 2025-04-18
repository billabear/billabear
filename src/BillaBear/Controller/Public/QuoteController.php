<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\QuoteDataMapper;
use BillaBear\Dto\Request\Public\ProcessPay;
use BillaBear\Dto\Response\Portal\Quote\StripeInfo;
use BillaBear\Dto\Response\Portal\Quote\ViewPay;
use BillaBear\Entity\Quote;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Quotes\QuoteConverter;
use BillaBear\Repository\QuoteRepositoryInterface;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuoteController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/quote/{hash}/pay', name: 'app_public_quote_readpay', methods: ['GET'])]
    public function readPay(
        Request $request,
        QuoteRepositoryInterface $quoteRepository,
        QuoteDataMapper $quoteDataMapper,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        FrontendConfig $config,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        try {
            /** @var Quote $quote */
            $quote = $quoteRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultSettings = $settingsRepository->getDefaultSettings();
        $apiKey = empty($config->getApiInfo()) ? $defaultSettings->getSystemSettings()->getStripePublicKey() : $config->getApiInfo();

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($quote->getCustomer()));
        $stripe->setKey($apiKey);
        $viewDto = new ViewPay();
        $viewDto->setStripe($stripe);
        $viewDto->setQuote($quoteDataMapper->createPublicDto($quote));

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/quote/{hash}/pay', name: 'app_public_quote_processpay', methods: ['POST'])]
    public function processPay(
        Request $request,
        QuoteRepositoryInterface $quoteRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceCharger $invoiceCharger,
        QuoteConverter $quoteConverter,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        try {
            /** @var Quote $quote */
            $quote = $quoteRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $now = new \DateTime();
        if (null !== $quote->getExpiresAt() && $quote->getExpiresAt() < $now) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        $processPay = $serializer->deserialize($request->getContent(), ProcessPay::class, 'json');
        $errors = $validator->validate($processPay);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse) {
            return $errorResponse;
        }
        $paymentCard = $addCardByTokenDriver->createPaymentDetailsFromToken($quote->getCustomer(), $processPay->getToken());
        $invoice = $quoteConverter->convertToInvoice($quote);
        $quoteRepository->save($quote);

        $success = true;
        $failureReason = false;
        try {
            $invoiceCharger->chargeInvoice($invoice, $paymentCard);
            foreach ($invoice->getSubscriptions() as $subscription) {
                $eventDispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);
            }
        } catch (PaymentFailureException $exception) {
            $success = false;
            $failureReason = $exception->getReason()->value;
        }

        return new JsonResponse(['success' => $success, 'failure_reason' => $failureReason]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
