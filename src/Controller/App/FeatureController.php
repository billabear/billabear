<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Dto\Request\App\PostFeature;
use App\Dto\Response\Api\ListResponse;
use App\Factory\FeatureFactory;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeatureController
{
    #[Route('/app/feature', name: 'app_feature_create', methods: ['POST'])]
    public function createFeature(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FeatureFactory $featureFactory,
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
        FeatureFactory $featureFactory,
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
