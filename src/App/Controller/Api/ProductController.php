<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Api;

use App\DataMappers\ProductDataMapper;
use App\Dto\Request\Api\CreateProduct;
use App\Dto\Response\Api\ListResponse;
use App\Filters\ProductList;
use Obol\Exception\ProviderFailureException;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController
{
    #[Route('/api/v1/product', name: 'api_v1.0_product_create', methods: ['POST'])]
    public function createProduct(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProductDataMapper $productFactory,
        ProductRegisterInterface $productRegister,
        ProductRepositoryInterface $productRepository,
    ): Response {
        $dto = $serializer->deserialize($request->getContent(), CreateProduct::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = $productFactory->createFromApiCreate($dto);
        if (!$product->hasExternalReference()) {
            try {
                $product = $productRegister->registerProduct($product);
            } catch (ProviderFailureException $e) {
                return new JsonResponse([], JsonResponse::HTTP_FAILED_DEPENDENCY);
            }
        }
        $productRepository->save($product);
        $productDto = $productFactory->createApiDtoFromProduct($product);
        $jsonResponse = $serializer->serialize($productDto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/product', name: 'api_v1.0_product_list', methods: ['GET'])]
    public function listProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SerializerInterface $serializer,
        ProductDataMapper $productFactory,
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

        $resultSet = $productRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$productFactory, 'createApiDtoFromProduct'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/product/{id}', name: 'api_v1.0_product_read', methods: ['GET'])]
    public function viewProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SerializerInterface $serializer,
        ProductDataMapper $productFactory,
    ): Response {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $dto = $productFactory->createApiDtoFromProduct($product);
        $data = $serializer->serialize($dto, 'json');

        return new JsonResponse($data, json: true);
    }

    #[Route('/api/v1/product/{id}', name: 'api_v1.0_product_update', methods: ['PUT'])]
    public function updateProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProductDataMapper $productFactory,
    ): Response {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var CreateProduct $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateProduct::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $newProduct = $productFactory->createFromApiCreate($dto, $product);

        $productRepository->save($newProduct);
        $dto = $productFactory->createApiDtoFromProduct($newProduct);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
