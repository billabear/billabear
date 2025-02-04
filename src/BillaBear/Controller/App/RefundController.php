<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Dto\Response\App\RefundView;
use BillaBear\Filters\RefundList;
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

    #[Route('/app/refund', name: 'app_refund_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        RefundRepositoryInterface $repository,
        SerializerInterface $serializer,
        RefundDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to list payment');

        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new RefundList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $repository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$factory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/refund/{id}', name: 'app_refund_view', methods: ['GET'])]
    public function veiwRefund(
        Request $request,
        RefundRepositoryInterface $repository,
        RefundDataMapper $factory,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to refund', ['refund_id' => $request->get('id')]);

        try {
            $refund = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $factory->createAppDto($refund);
        $dtoView = new RefundView();
        $dtoView->setRefund($dto);
        $json = $serializer->serialize($dtoView, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
