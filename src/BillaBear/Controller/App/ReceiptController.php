<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Database\TransactionManager;
use BillaBear\DataMappers\ReceiptDataMapper;
use BillaBear\Dummy\Data\ReceiptProvider;
use BillaBear\Pdf\ReceiptPdfGenerator;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class ReceiptController
{
    use LoggerAwareTrait;

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/payment/{id}/generate-receipt', name: 'app_payment_receipt', methods: ['POST'])]
    public function generateForPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        ReceiptRepositoryInterface $receiptRepository,
        ReceiptGeneratorInterface $receiptGenerator,
        ReceiptDataMapper $receiptFactory,
        SerializerInterface $serializer,
        TransactionManager $transactionManager,
    ): Response {
        $this->getLogger()->info('Received request to generate receipt for payment', ['payment_id' => $request->get('id')]);

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
        $this->getLogger()->info('Received request to download receipt for payment', ['receipt_id' => $request->get('id')]);

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
