<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Public;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\InvoiceDataMapper;
use App\Dto\Request\Public\ProcessPay;
use App\Dto\Response\Portal\Invoice\StripeInfo;
use App\Dto\Response\Portal\Invoice\ViewPay;
use App\Entity\Invoice;
use App\Payment\InvoiceCharger;
use App\Repository\InvoiceRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceController
{
    use ValidationErrorResponseTrait;

    #[Route('/public/invoice/{hash}/pay', name: 'app_public_invoice_readpay', methods: ['GET'])]
    public function readPay(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        FrontendConfig $config,
    ): Response {
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($invoice->getCustomer()));
        $stripe->setKey($config->getApiInfo());
        $viewDto = new ViewPay();
        $viewDto->setStripe($stripe);
        $viewDto->setInvoice($invoiceDataMapper->createPublicDto($invoice));

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/invoice/{hash}/pay', name: 'app_public_invoice_processpay', methods: ['POST'])]
    public function processPay(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        FrontendConfig $config,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceCharger $invoiceCharger,
    ): Response {
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $processPay = $serializer->deserialize($request->getContent(), ProcessPay::class, 'json');
        $errors = $validator->validate($processPay);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse) {
            return $errorResponse;
        }
        $paymentCard = $addCardByTokenDriver->createPaymentDetailsFromToken($invoice->getCustomer(), $processPay->getToken());
        $success = $invoiceCharger->chargeInvoice($invoice, $paymentCard);

        return new JsonResponse(['success' => $success]);
    }
}
