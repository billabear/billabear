<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Dto\Response\App\ListResponse;
use App\Filters\AbstractFilterList;
use App\Filters\PaymentList;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

trait CrudListTrait
{
    protected function crudList(Request $request, CrudRepositoryInterface $crudRepository, SerializerInterface $serializer, $dataMapper, string $defaultSortKey = 'createdAt', ?AbstractFilterList $filterList = null): Response
    {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $key = $request->get('key', $defaultSortKey);
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

        if (null === $filterList) {
            $filterList = new PaymentList();
        }
        $filters = $filterList->buildFilters($request);

        if ($lastKey && 'createdAt' === $key) {
            $lastKey = new \DateTime($lastKey);
        }
        if ($firstKey && 'createdAt' === $key) {
            $firstKey = new \DateTime($firstKey);
        }
        $resultSet = $crudRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
            sortKey: $key,
            sortType: 'DESC',
        );

        $dtos = array_map([$dataMapper, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }
}
