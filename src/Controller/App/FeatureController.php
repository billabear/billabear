<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\DataMappers\FeatureDataMapper;
use App\Dto\Request\App\PostFeature;
use App\Dto\Response\Api\ListResponse;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeatureController
{
    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/feature', name: 'app_feature_create', methods: ['POST'])]
    public function createFeature(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FeatureDataMapper $featureFactory,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
    ): Response {
        $dto = $serializer->deserialize($request->getContent(), PostFeature::class, 'json');
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

        $feature = $featureFactory->createFromPostFeature($dto);
        $subscriptionFeatureRepository->save($feature);
        $featureDto = $featureFactory->createAppDto($feature);
        $jsonResponse = $serializer->serialize($featureDto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/app/feature', name: 'app_feature_list', methods: ['GET'])]
    public function listFeatures(
        Request $request,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        SerializerInterface $serializer,
        FeatureDataMapper $featureFactory,
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

        $filters = [];

        $resultSet = $subscriptionFeatureRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$featureFactory, 'createAppDto'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }
}
