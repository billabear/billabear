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

namespace App\Controller\App;

use App\Database\TransactionManager;
use App\Dummy\Data\ReceiptProvider;
use App\Entity\Receipt;
use App\Factory\ReceiptFactory;
use App\Pdf\ReceiptPdfGenerator;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class ReceiptController
{
    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/payment/{id}/generate-receipt', name: 'app_payment_receipt', methods: ['POST'])]
    public function generateForPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        ReceiptRepositoryInterface $receiptRepository,
        ReceiptGeneratorInterface $receiptGenerator,
        ReceiptFactory $receiptFactory,
        SerializerInterface $serializer,
        TransactionManager $transactionManager
    ): Response {
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $transactionManager->start();
        try {
            $receipt = $receiptGenerator->generateReceiptForPayment($payment);
            $receiptRepository->save($receipt);
        } catch (\Throwable $exception) {
            $transactionManager->abort();
            throw $exception;
        }
        $transactionManager->finish();
        $dto = $receiptFactory->createAppDto($receipt);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/receipt/{id}/download', name: 'app_payment_download', methods: ['GET'])]
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
