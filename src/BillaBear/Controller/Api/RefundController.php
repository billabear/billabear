<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Filters\ProductList;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RefundController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/refund', name: 'api_v1.0_refund_list', methods: ['GET'])]
    public function listRefund(
        Request $request,
        RefundRepositoryInterface $repository,
        SerializerInterface $serializer,
        RefundDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to list refunds');
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

    #[Route('/api/v1/refund/{id}', name: 'api_v1.0_refund_view', methods: ['GET'])]
    public function viewRefund(
        Request $request,
        RefundRepositoryInterface $repository,
        RefundDataMapper $factory,
        SerializerInterface $serializer,
    ): JsonResponse {
        $this->getLogger()->info('Received request to view refund', ['refund_id' => $request->get('id')]);
        try {
            $refund = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse(status: Response::HTTP_NOT_FOUND);
        }

        $dto = $factory->createApiDto($refund);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
