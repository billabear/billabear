<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\Dto\Request\Api\CreatePrice;
use BillaBear\Dto\Response\Api\ListResponse;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/product/{id}/price', name: 'api_v1.0_product_price_create', methods: ['POST'])]
    public function createPrice(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PriceRepositoryInterface $priceRepository,
        ProductRepositoryInterface $productRepository,
        PriceDataMapper $priceFactory,
    ): JsonResponse {
        $this->getLogger()->info('Received request to create price for product', [
            'product_id' => $request->get('id'),
        ]);
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        /** @var CreatePrice $dto */
        $dto = $serializer->deserialize($request->getContent(), CreatePrice::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], Response::HTTP_BAD_REQUEST);
        }

        $price = $priceFactory->createPriceFromDto($dto);
        $price->setProduct($product);

        $priceRepository->save($price);
        $dto = $priceFactory->createApiDto($price);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/product/{id}/price', name: 'api_v1.0_product_price_list', methods: ['GET'])]
    public function listProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        PriceDataMapper $priceFactory,
    ): Response {
        $this->getLogger()->info('Received request to list prices for product', [
            'product_id' => $request->get('id'),
        ]);
        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
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
        // TODO add filters
        $filters = [];
        $productFilter = new ExactChoiceFilter();
        $productFilter->setFieldName('product');
        $productFilter->setData($product);
        $filters[] = $filters;

        $resultSet = $priceRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$priceFactory, 'createApiDto'], $resultSet->getResults());

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
