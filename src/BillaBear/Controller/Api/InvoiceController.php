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
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceController
{
    #[Route('/api/v1/customer/{customerId}/invoices', name: 'billabear_api_invoice_getinvoicesforcustomer', methods: ['GET'])]
    public function getInvoicesForCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
    ): Response {
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
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $invoiceCharger->chargeInvoice($invoice);

        return new JsonResponse(['paid' => $invoice->isPaid()], JsonResponse::HTTP_OK);
    }
}
