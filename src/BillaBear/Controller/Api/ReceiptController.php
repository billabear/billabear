<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Dummy\Data\ReceiptProvider;
use BillaBear\Pdf\ReceiptPdfGenerator;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ReceiptController
{
    #[Route('/api/v1/receipt/{id}/download', name: 'billabear_api_receipt_downloadreceipt', methods: ['GET'])]
    public function downloadReceipt(
        Request $request,

        ReceiptRepositoryInterface $receiptRepository,
        ReceiptPdfGenerator $generator,
        ReceiptProvider $provider,
    ): Response {
        try {
            /** @var Receipt $receipt */
            $receipt = $receiptRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $pdf = $generator->generate($receipt);
        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $pdf);

        $response = new BinaryFileResponse($tmpFile);
        $filename = 'receipt.pdf';

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
