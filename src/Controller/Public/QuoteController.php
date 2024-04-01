<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Public;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\QuoteDataMapper;
use App\Dto\Request\Public\ProcessPay;
use App\Dto\Response\Portal\Quote\StripeInfo;
use App\Dto\Response\Portal\Quote\ViewPay;
use App\Entity\Quote;
use App\Payment\InvoiceCharger;
use App\Quotes\QuoteConverter;
use App\Repository\QuoteRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuoteController
{
    use ValidationErrorResponseTrait;

    #[Route('/public/quote/{hash}/pay', name: 'app_public_quote_readpay', methods: ['GET'])]
    public function readPay(
        Request $request,
        QuoteRepositoryInterface $quoteRepository,
        QuoteDataMapper $quoteDataMapper,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        FrontendConfig $config,
    ): Response {
        try {
            /** @var Quote $quote */
            $quote = $quoteRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($quote->getCustomer()));
        $stripe->setKey($config->getApiInfo());
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
        FrontendConfig $config,
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
        $success = $invoiceCharger->chargeInvoice($invoice, $paymentCard);
        if ($success) {
            foreach ($invoice->getSubscriptions() as $subscription) {
                $eventDispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);
            }
        }

        return new JsonResponse(['success' => $success]);
    }
}
