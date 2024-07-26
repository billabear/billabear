<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\InvoiceDataMapper;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Entity\Invoice;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use Obol\Exception\PaymentFailureException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceController
{
    use LoggerAwareTrait;

    #[Route('/api/v1/customer/{customerId}/invoices', name: 'billabear_api_invoice_getinvoicesforcustomer', methods: ['GET'])]
    public function getInvoicesForCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received API request for customer invoices', ['customer_id' => $request->get('customerId')]);

        try {
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $invoices = $invoiceRepository->getAllForCustomer($customer);
        $dtos = array_map([$invoiceDataMapper, 'createApiDto'], $invoices);
        $listView = new ListResponse();
        $listView->setData($dtos);
        $listView->setHasMore(false);
        $json = $serializer->serialize($listView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/invoice/{id}/charge', name: 'billabear_api_invoice_chargeinvoice', methods: ['POST'])]
    public function chargeInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceCharger $invoiceCharger
    ): Response {
        $this->getLogger()->info('Received an API request to charge invoice', ['invoice_id' => $request->get('id')]);
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $failureReason = null;
        $statusCode = JsonResponse::HTTP_OK;
        try {
            $invoiceCharger->chargeInvoice($invoice);
        } catch (PaymentFailureException $e) {
            $failureReason = $e->getReason()->value;
            $statusCode = JsonResponse::HTTP_PAYMENT_REQUIRED;
        }

        return new JsonResponse(['paid' => $invoice->isPaid(), 'failure_reason' => $failureReason], $statusCode);
    }

    #[Route('/api/v1/invoice/{id}/download', name: 'billabear_api_invoice_downloadinvoice', methods: ['GET'])]
    public function downloadInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceFormatterProvider $invoiceFormatterProvider,
    ): Response {
        $this->getLogger()->info('Received an API request to download invoice', ['invoice_id' => $request->get('id')]);
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $generator = $invoiceFormatterProvider->getFormatter($invoice->getCustomer());
        $pdf = $generator->generate($invoice);
        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $pdf);

        $response = new BinaryFileResponse($tmpFile);
        $filename = $generator->filename($invoice);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
