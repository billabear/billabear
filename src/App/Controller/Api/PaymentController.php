<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Api;

use App\DataMappers\PaymentDataMapper;
use App\Dto\Request\Api\Payments\RefundPayment;
use App\Dto\Response\Api\ListResponse;
use App\Filters\ProductList;
use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Exception\RefundLimitExceededException;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentController
{
    #[Route('/api/v1/payment', name: 'api_v1.0_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $repository,
        SerializerInterface $serializer,
        PaymentDataMapper $factory,
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
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ) {
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var RefundPayment $dto */
        $dto = $serializer->deserialize($request->getContent(), RefundPayment::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $amount = Money::ofMinor($dto->getAmount(), Currency::of($dto->getCurrency() ?? $payment->getCurrency()));
        try {
            $refundManager->issueRefundForPayment($payment, $amount, null, $dto->getReason());
        } catch (RefundLimitExceededException $e) {
            return new JsonResponse(['message' => $e->getMessage()], status: JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }
}
