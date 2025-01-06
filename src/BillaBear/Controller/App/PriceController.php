<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\DataMappers\Usage\MetricDataMapper;
use BillaBear\Dto\Request\Api\CreatePrice;
use BillaBear\Dto\Request\App\Price\CreatePriceView;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\PriceRegisterInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController
{
    use LoggerAwareTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/price', name: 'app_product_price_create_get', methods: ['GET'])]
    public function createPriceRead(
        MetricRepositoryInterface $metricRepository,
        MetricDataMapper $metricDataMapper,
        SerializerInterface $serializer,
    ) {
        $metrics = $metricRepository->getAll();
        $dto = new CreatePriceView();
        $dto->setMetrics(array_map([$metricDataMapper, 'createAppDto'], $metrics));

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, status: Response::HTTP_OK, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/price', name: 'app_product_price_create', methods: ['POST'])]
    public function createPrice(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PriceRepositoryInterface $priceRepository,
        ProductRepositoryInterface $productRepository,
        PriceDataMapper $priceFactory,
        PriceRegisterInterface $priceRegister,
    ) {
        $this->getLogger()->info('Received request to create price', ['product_id' => $request->get('id')]);

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

        $priceRepository->save($price);
        $dto = $priceFactory->createAppDto($price);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/app/price', name: 'app_price_list', methods: ['GET'])]
    public function listPrices(
        Request $request,
        ProductRepositoryInterface $productRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        PriceDataMapper $priceFactory,
    ): Response {
        $this->getLogger()->info('Received request to lsit prices');

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
        // TODO add filters
        $filters = [];

        $resultSet = $priceRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$priceFactory, 'createAppDto'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/price/{priceId}/delete', name: 'app_product_price_delete', methods: ['POST'])]
    public function deletePrice(
        Request $request,
        PriceRepositoryInterface $priceRepository,
    ) {
        $this->getLogger()->info('Received request to delete price', ['product_id' => $request->get('id'), 'price_id' => $request->get('priceId')]);

        try {
            /** @var Price $price */
            $price = $priceRepository->findById($request->get('priceId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $price->markAsDeleted();
        $priceRepository->save($price);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/price/{priceId}/private', name: 'app_product_price_private', methods: ['POST'])]
    public function privatePrice(
        Request $request,
        PriceRepositoryInterface $priceRepository,
    ) {
        $this->getLogger()->info('Received request to make price private', [
            'product_id' => $request->get('id'),
            'price_id' => $request->get('priceId'),
        ]);

        try {
            /** @var Price $price */
            $price = $priceRepository->findById($request->get('priceId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $price->setPublic(false);
        $priceRepository->save($price);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/price/{priceId}/public', name: 'app_product_price_public', methods: ['POST'])]
    public function publicPrice(
        Request $request,
        PriceRepositoryInterface $priceRepository,
    ) {
        $this->getLogger()->info('Received request to make price public', [
            'product_id' => $request->get('id'),
            'price_id' => $request->get('priceId'),
        ]);

        try {
            /** @var Price $price */
            $price = $priceRepository->findById($request->get('priceId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $price->setPublic(true);
        $priceRepository->save($price);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
