<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Subscriptions;

use BillaBear\DataMappers\FeatureDataMapper;
use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\DataMappers\Usage\MetricDataMapper;
use BillaBear\Dto\Request\App\Product\UpdateSubscriptionPlan;
use BillaBear\Dto\Request\App\Subscription\PostSubscriptionPlan;
use BillaBear\Dto\Response\App\SubscriptionPlanCreationInfo;
use BillaBear\Dto\Response\App\SubscriptionPlanUpdateView;
use BillaBear\Dto\Response\App\SubscriptionPlanView;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Subscription\PlanCreatedPayload;
use BillaBear\Webhook\Outbound\Payload\Subscription\PlanDeletePayload;
use BillaBear\Webhook\Outbound\Payload\Subscription\PlanUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcher;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionPlanController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/plan-creation', name: 'app_product_plan_create_info', methods: ['get'])]
    public function planCreationInfo(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        FeatureDataMapper $featureFactory,
        PriceRepositoryInterface $priceRepository,
        PriceDataMapper $priceFactory,
        MetricRepositoryInterface $metricRepository,
        MetricDataMapper $metricDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to read create plan', ['product_id' => $request->get('id')]);

        try {
            /** @var Product $product */
            $product = $productRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $features = $subscriptionFeatureRepository->getAll();
        $prices = $priceRepository->getAllForProduct($product);
        $metrics = $metricRepository->getAll();

        $featureDtos = array_map([$featureFactory, 'createAppDto'], $features);
        $priceDtos = array_map([$priceFactory, 'createAppDto'], $prices);
        $metricDtos = array_map([$metricDataMapper, 'createAppDto'], $metrics);

        $dto = new SubscriptionPlanCreationInfo();
        $dto->setPrices($priceDtos);
        $dto->setFeatures($featureDtos);
        $dto->setMetrics($metricDtos);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{id}/plan', name: 'app_product_plan_create', methods: ['POST'])]
    public function createPlan(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionPlanDataMapper $factory,
        ProductRepositoryInterface $productRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        WebhookDispatcher $eventDispatcher,
    ) {
        $this->getLogger()->info('Received request to write create plan', ['product_id' => $request->get('id')]);

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

        $eventDispatcher->dispatch(new PlanCreatedPayload($plan));

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/app/product/{productId}/plan/{id}', name: 'app_product_plan_view', methods: ['GET'])]
    public function viewPlan(
        Request $request,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        SubscriptionPlanDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to write create plan', ['product_id' => $request->get('productId'), 'plan_id' => $request->get('id')]);
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
    #[Route('/app/product/{productId}/plan/{id}', name: 'app_product_plan_delete', methods: ['DELETE'])]
    public function deletePlan(
        Request $request,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        WebhookDispatcher $eventDispatcher,
    ): Response {
        $this->getLogger()->info('Received request to delete plan', ['product_id' => $request->get('productId'), 'plan_id' => $request->get('id')]);

        try {
            /** @var SubscriptionPlan $subscriptionPlan */
            $subscriptionPlan = $subscriptionPlanRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionPlan->markAsDeleted();
        $subscriptionPlanRepository->save($subscriptionPlan);

        $eventDispatcher->dispatch(new PlanDeletePayload($subscriptionPlan));

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{productId}/plan/{id}/update', name: 'app_product_plan_update_view', methods: ['GET'])]
    public function updateViewPlan(
        Request $request,
        ProductRepositoryInterface $productRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        SubscriptionPlanDataMapper $factory,
        SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        FeatureDataMapper $featureFactory,
        PriceRepositoryInterface $priceRepository,
        MetricRepositoryInterface $metricRepository,
        MetricDataMapper $metricDataMapper,
        PriceDataMapper $priceFactory,
    ): Response {
        $this->getLogger()->info('Received request to read update plan', ['product_id' => $request->get('productId'), 'plan_id' => $request->get('id')]);

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
        $metrics = $metricRepository->getAll();

        $featureDtos = array_map([$featureFactory, 'createAppDto'], $features);
        $priceDtos = array_map([$priceFactory, 'createAppDto'], $prices);
        $metricDtos = array_map([$metricDataMapper, 'createAppDto'], $metrics);

        $subscriptionPlanDto = $factory->createAppDto($subscriptionPlan);
        $dto = new SubscriptionPlanUpdateView();
        $dto->setPrices($priceDtos);
        $dto->setFeatures($featureDtos);
        $dto->setSubscriptionPlan($subscriptionPlanDto);
        $dto->setMetrics($metricDtos);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/product/{productId}/plan/{id}/update', name: 'app_product_plan_update', methods: ['POST'])]
    public function updatePlan(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionPlanDataMapper $factory,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        WebhookDispatcher $eventDispatcher,
    ) {
        $this->getLogger()->info('Received request to write update plan', ['product_id' => $request->get('productId'), 'plan_id' => $request->get('id')]);

        try {
            /** @var SubscriptionPlan $subscriptionPlan */
            $subscriptionPlan = $subscriptionPlanRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var UpdateSubscriptionPlan $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdateSubscriptionPlan::class, 'json');
        $dto->setId($subscriptionPlan->getId());
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

        $eventDispatcher->dispatch(new PlanUpdatedPayload($subscriptionPlan));

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
