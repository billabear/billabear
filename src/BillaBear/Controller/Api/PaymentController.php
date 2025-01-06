<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\PaymentDataMapper;
use BillaBear\Dto\Request\Api\Payments\RefundPayment;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Filters\ProductList;
use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Exception\RefundLimitExceededException;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentController
{
    use LoggerAwareTrait;

    #[Route('/api/v1/payment', name: 'api_v1.0_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $repository,
        SerializerInterface $serializer,
        PaymentDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request for list of all payments');
        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'reason' => 'limit is below 1',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'reason' => 'limit is above 100',
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
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
    ): JsonResponse {
        $this->getLogger()->info('Received request to refund payment', ['payment_id' => $request->get('id')]);
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse(status: Response::HTTP_NOT_FOUND);
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
            ], Response::HTTP_BAD_REQUEST);
        }
        $amount = Money::ofMinor($dto->getAmount(), Currency::of($dto->getCurrency() ?? $payment->getCurrency()));
        try {
            $refundManager->issueRefundForPayment($payment, $amount, null, $dto->getReason());
        } catch (RefundLimitExceededException $e) {
            return new JsonResponse(['message' => $e->getMessage()], status: Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse(status: Response::HTTP_ACCEPTED);
    }
}
