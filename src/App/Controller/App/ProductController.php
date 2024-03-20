<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\DataMappers\PriceDataMapper;
use App\DataMappers\ProductDataMapper;
use App\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use App\DataMappers\TaxTypeDataMapper;
use App\Dto\Request\App\CreateProduct;
use App\Dto\Response\Api\ListResponse;
use App\Dto\Response\App\Product\CreateProductView;
use App\Dto\Response\App\ProductView;
use App\Filters\ProductList;
use App\Repository\SubscriptionPlanRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use Obol\Exception\ProviderFailureException;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController
{
    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/create', name: 'app_product_create_view', methods: ['GET'])]
    public function createProductView(
        Request $request,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer,
    ) {
        $taxTypes = $taxTypeRepository->getAll();
        $taxTypesDto = array_map([$taxTypeDataMapper, 'createAppDto'], $taxTypes);

        $viewDto = new CreateProductView();
        $viewDto->setTaxTypes($taxTypesDto);

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product', name: 'app_product_create', methods: ['POST'])]
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

        $product = $productFactory->createFromAppCreate($dto);
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

    #[Route('/app/product', name: 'app_product_list', methods: ['GET'])]
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

    #[Route('/app/product/{id}', name: 'app_product_view', methods: ['GET'])]
    public function viewProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        PriceRepositoryInterface $priceRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        ProductDataMapper $productFactory,
        PriceDataMapper $priceFactory,
        SubscriptionPlanDataMapper $subscriptionPlanFactory,
    ): Response {
        try {
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $prices = $priceRepository->getAllForProduct($product);
        $prices = array_filter($prices, function ($price) {
            return !$price->isDeleted();
        });
        $pricesDtos = array_map([$priceFactory, 'createAppDto'], $prices);

        $plans = $subscriptionPlanRepository->getNonDeletedForProduct($product);
        $planDtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $plans);

        $productDto = $productFactory->createAppDtoFromProduct($product);
        $dto = new ProductView();
        $dto->setProduct($productDto);
        $dto->setPrices($pricesDtos);
        $dto->setSubscriptionPlans($planDtos);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}', name: 'app_product_update', methods: ['POST'])]
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

        $newProduct = $productFactory->createFromAppCreate($dto, $product);

        $productRepository->save($newProduct);
        $dto = $productFactory->createAppDtoFromProduct($newProduct);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
