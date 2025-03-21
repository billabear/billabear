<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api\Customer;

use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Entity\Customer;
use BillaBear\Filters\RefundList;
use BillaBear\Repository\CustomerRepositoryInterface;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerRefundController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/customer/{id}/refund', name: 'api_v1.0_customer_refund_list', methods: ['GET'])]
    public function listRefund(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        RefundRepositoryInterface $repository,
        SerializerInterface $serializer,
        RefundDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received API request for list refunds for customer', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

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

        $filterBuilder = new RefundList();
        $filters = $filterBuilder->buildFilters($request);
        $customerFilter = new ExactChoiceFilter();
        $customerFilter->setFieldName('customer');
        $customerFilter->setData($customer);
        $filters[] = $customerFilter;

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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
