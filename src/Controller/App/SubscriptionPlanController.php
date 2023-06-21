<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Dto\Request\App\PostSubscriptionPlan;
use App\Dto\Response\App\SubscriptionPlanCreationInfo;
use App\Dto\Response\App\SubscriptionPlanUpdateView;
use App\Dto\Response\App\SubscriptionPlanView;
use App\Factory\FeatureFactory;
use App\Factory\PriceFactory;
use App\Factory\SubscriptionPlanFactory;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionPlanController
{
    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/plan-creation', name: 'app_product_plan_create_info', methods: ['get'])]
    public function planCreationInfo(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        FeatureFactory $featureFactory,
        PriceRepositoryInterface $priceRepository,
        PriceFactory $priceFactory,
        SerializerInterface $serializer
    ): Response {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $features = $subscriptionFeatureRepository->getAll();
        $prices = $priceRepository->getAllForProduct($product);

        $featureDtos = array_map([$featureFactory, 'createAppDto'], $features);
        $priceDtos = array_map([$priceFactory, 'createAppDto'], $prices);

        $dto = new SubscriptionPlanCreationInfo();
        $dto->setPrices($priceDtos);
        $dto->setFeatures($featureDtos);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/plan', name: 'app_product_plan_create', methods: ['POST'])]
    public function createPlan(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionPlanFactory $factory,
        ProductRepositoryInterface $productRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
    ) {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var PostSubscriptionPlan $dto */
        $dto = $serializer->deserialize($request->getContent(), PostSubscriptionPlan::class, 'json');
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

        $plan = $factory->createFromPostSubscriptionPlan($dto);
        $plan->setProduct($product);
        $subscriptionPlanRepository->save($plan);
        $dto = $factory->createAppDto($plan);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/app/product/{productId}/plan/{id}', name: 'app_product_plan_view', methods: ['GET'])]
    public function viewPlan(
        Request $request,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        SubscriptionPlanFactory $factory,
    ): Response {
        try {
            $subscriptionPlan = $subscriptionPlanRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionPlanDto = $factory->createAppDto($subscriptionPlan);
        $dto = new SubscriptionPlanView();
        $dto->setSubscriptionPlan($subscriptionPlanDto);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{productId}/plan/{id}/update', name: 'app_product_plan_update_view', methods: ['GET'])]
    public function updateViewPlan(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        SubscriptionPlanFactory $factory,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        FeatureFactory $featureFactory,
        PriceRepositoryInterface $priceRepository,
        PriceFactory $priceFactory,
    ): Response {
        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('productId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        try {
            $subscriptionPlan = $subscriptionPlanRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $features = $subscriptionFeatureRepository->getAll();
        $prices = $priceRepository->getAllForProduct($product);

        $featureDtos = array_map([$featureFactory, 'createAppDto'], $features);
        $priceDtos = array_map([$priceFactory, 'createAppDto'], $prices);

        $subscriptionPlanDto = $factory->createAppDto($subscriptionPlan);
        $dto = new SubscriptionPlanUpdateView();
        $dto->setPrices($priceDtos);
        $dto->setFeatures($featureDtos);
        $dto->setSubscriptionPlan($subscriptionPlanDto);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{productId}/plan/{id}/update', name: 'app_product_plan_update', methods: ['POST'])]
    public function updatePlan(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionPlanFactory $factory,
        ProductRepositoryInterface $productRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
    ) {
        try {
            $subscriptionPlan = $subscriptionPlanRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var PostSubscriptionPlan $dto */
        $dto = $serializer->deserialize($request->getContent(), PostSubscriptionPlan::class, 'json');
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

        $plan = $factory->createFromPostSubscriptionPlan($dto, $subscriptionPlan);
        $subscriptionPlanRepository->save($plan);
        $dto = $factory->createAppDto($plan);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
