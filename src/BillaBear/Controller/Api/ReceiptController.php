<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Pdf\ReceiptPdfGenerator;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ReceiptController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/receipt/{id}/download', name: 'billabear_api_receipt_downloadreceipt', methods: ['GET'])]
    public function downloadReceipt(
        Request $request,

        ReceiptRepositoryInterface $receiptRepository,
        ReceiptPdfGenerator $generator,
    ): Response {
        try {
            /** @var Receipt $receipt */
            $receipt = $receiptRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info('Received request to download receipt', [
            'receipt_id' => $request->get('id'),
            'customer_id' => (string) $receipt->getCustomer()->getId(),
        ]);
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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
