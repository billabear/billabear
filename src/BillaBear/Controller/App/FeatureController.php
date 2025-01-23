<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\FeatureDataMapper;
use BillaBear\Dto\Request\App\PostFeature;
use BillaBear\Dto\Response\Api\ListResponse;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeatureController
{
    use LoggerAwareTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/feature', name: 'app_feature_create', methods: ['POST'])]
    public function createFeature(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FeatureDataMapper $featureFactory,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
    ): Response {
        $this->getLogger()->info('Received request to create feature');

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
        $this->getLogger()->info('Received request to list features');
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
