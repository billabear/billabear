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
}
