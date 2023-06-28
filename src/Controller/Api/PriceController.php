<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Api;

use App\Dto\Request\Api\CreatePrice;
use App\Dto\Response\Api\ListResponse;
use App\Factory\PriceFactory;
use Obol\Exception\ProviderFailureException;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\PriceRegisterInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController
{
    #[Route('/api/v1/product/{id}/price', name: 'api_v1.0_product_price_create', methods: ['POST'])]
    public function createPrice(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PriceRepositoryInterface $priceRepository,
        ProductRepositoryInterface $productRepository,
        PriceFactory $priceFactory,
        PriceRegisterInterface $priceRegister,
    ) {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
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
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $price = $priceFactory->createPriceFromDto($dto);
        $price->setProduct($product);

        if (!$price->getExternalReference()) {
            try {
                $priceRegister->registerPrice($price);
            } catch (ProviderFailureException $e) {
                return new JsonResponse([], JsonResponse::HTTP_FAILED_DEPENDENCY);
            }
        }
        $priceRepository->save($price);
        $dto = $priceFactory->createApiDto($price);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/product/{id}/price', name: 'api_v1.0_product_price_list', methods: ['GET'])]
    public function listProduct(
        Request $request,
        ProductRepositoryInterface $productRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        PriceFactory $priceFactory,
    ): Response {
        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
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
        // TODO add filters
        $filters = [];

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
}
