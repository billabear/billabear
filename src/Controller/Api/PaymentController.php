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

namespace App\Controller\Api;

use App\Api\Filters\ProductList;
use App\Dto\Request\Api\Payments\RefundPayment;
use App\Dto\Response\Api\ListResponse;
use App\Factory\PaymentFactory;
use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Billing\Exception\RefundLimitExceededException;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentController
{
    #[Route('/api/v1/payment', name: 'api_v1.0_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $repository,
        SerializerInterface $serializer,
        PaymentFactory $factory,
    ): Response {
        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'reason' => 'limit is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'reason' => 'limit is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new ProductList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $repository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$factory, 'createApiDto'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/payment/{id}/refund', name: 'api_v1.0_payment_refund', methods: ['POST'])]
    public function refundPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        RefundManagerInterface $refundManager,
        SerializerInterface $serializer,
    ) {
        try {
            $payment = $paymentRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var RefundPayment $dto */
        $dto = $serializer->deserialize($request->getContent(), RefundPayment::class, 'json');
        $amount = Money::ofMinor($dto->getAmount(), Currency::of($dto->getCurrency()));
        try {
            $refundManager->issueRefundForPayment($payment, $amount, null, $dto->getReason());
        } catch (RefundLimitExceededException $e) {
            return new JsonResponse(['message' => $e->getMessage()], status: JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }
}
