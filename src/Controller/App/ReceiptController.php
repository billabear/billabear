<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceiptController
{
    #[Route('/app/payment/{id}/generate-receipt', name: 'app_payment_receipt', methods: ['POST'])]
    public function generateForPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        ReceiptRepositoryInterface $receiptRepository,
        ReceiptGeneratorInterface $receiptGenerator
    ): Response {
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $receipt = $receiptGenerator->generateReceiptForPayment($payment);
        $receiptRepository->save($receipt);

        return new JsonResponse([]);
    }
}
