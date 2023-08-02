<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Public;

use App\DataMappers\InvoiceDataMapper;
use App\Dto\Response\Portal\Invoice\StripeInfo;
use App\Dto\Response\Portal\Invoice\ViewPay;
use App\Entity\Invoice;
use App\Repository\InvoiceRepositoryInterface;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceController
{
    #[Route('/public/invoice/{hash}/pay', name: 'app_public_invoice_readpay', methods: ['GET'])]
    public function readPay(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
    ): Response {
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->findById($request->get('hash'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($invoice->getCustomer()));
        $viewDto = new ViewPay();
        $viewDto->setStripe($stripe);
        $viewDto->setInvoice($invoiceDataMapper->createPublicDto($invoice));

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
