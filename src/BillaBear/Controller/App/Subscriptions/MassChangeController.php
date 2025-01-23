<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Subscriptions;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\DataMappers\Subscriptions\MassChangeDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\Dto\Request\App\Subscription\MassChange\CreateMassChange;
use BillaBear\Dto\Request\App\Subscription\MassChange\EstimateMassChange;
use BillaBear\Dto\Response\App\Subscription\MassChange\CreateView;
use BillaBear\Dto\Response\App\Subscription\MassChange\ViewMassSubscriptionChange;
use BillaBear\Export\DataProvider\MassSubscriptionChangeCustomersDataProvider;
use BillaBear\Export\Response\ResponseConverter;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\MassSubscriptionChangeRepositoryInterface;
use BillaBear\Repository\SubscriptionPlanRepositoryInterface;
use BillaBear\Subscription\MassChange\RevenueEstimator;
use BillaBear\Subscription\MassSubscriptionChangeStatus;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Export\Engine\EngineInterface;
use Parthenon\Export\ExportRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MassChangeController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;
    use LoggerAwareTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/subscription/mass-change/create', name: 'app_app_subscriptions_masschange_createchangeread', methods: ['GET'])]
    public function createChangeRead(
        SerializerInterface $serializer,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        PriceRepositoryInterface $priceRepository,
        PriceDataMapper $priceDataMapper,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
        BrandSettingsDataMapper $brandSettingsDataMapper,
    ) {
        $this->getLogger()->info('Received request to read create mass subscription change');

        $prices = $priceRepository->getAll();
        $plans = $subscriptionPlanRepository->getAll();
        $brands = $brandSettingsRepository->getAll();

        $dto = new CreateView();
        $dto->setPlans(array_map([$subscriptionPlanDataMapper, 'createAppDto'], $plans));
        $dto->setPrices(array_map([$priceDataMapper, 'createAppDto'], $prices));
        $dto->setBrands(array_map([$brandSettingsDataMapper, 'createAppDto'], $brands));
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/subscription/mass-change', name: 'app_app_subscriptions_masschange_createchange', methods: ['POST'])]
    public function createChange(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MassChangeDataMapper $changeDataMapper,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
    ) {
        $this->getLogger()->info('Received request to write create mass subscription change');

        $dto = $serializer->deserialize($request->getContent(), CreateMassChange::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $changeDataMapper->createEntity($dto);
        $massSubscriptionChangeRepository->save($entity);
        $outDto = $changeDataMapper->createAppDto($entity);
        $json = $serializer->serialize($outDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/subscription/mass-change/estimate', name: 'app_app_subscriptions_masschange_estimate', methods: ['POST'])]
    public function estimate(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MassChangeDataMapper $changeDataMapper,
        RevenueEstimator $revenueEstimator,
    ) {
        $this->getLogger()->info('Received request to read create mass subscription estimate');
        $dto = $serializer->deserialize($request->getContent(), EstimateMassChange::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $changeDataMapper->createEntity($dto);
        $outDto = $revenueEstimator->generateEstimateDto($entity);

        $json = $serializer->serialize($outDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription/mass-change', name: 'app_app_subscriptions_masschange_listchange', methods: ['GET'])]
    public function listChange(
        Request $request,
        SerializerInterface $serializer,
        MassChangeDataMapper $changeDataMapper,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
    ) {
        $this->getLogger()->info('Received request to list mass subscriptions');

        return $this->crudList($request, $massSubscriptionChangeRepository, $serializer, $changeDataMapper);
    }

    #[Route('/app/subscription/mass-change/{id}/view', name: 'app_app_subscriptions_masschange_viewcange', methods: ['GET'])]
    public function viewChange(
        Request $request,
        SerializerInterface $serializer,
        MassChangeDataMapper $changeDataMapper,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
        RevenueEstimator $revenueEstimator,
    ) {
        $this->getLogger()->info('Received request to view mass subscriptions', ['mass_change_id' => $request->get('id')]);

        try {
            $entity = $massSubscriptionChangeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $viewDto = new ViewMassSubscriptionChange();
        $viewDto->setEstimate($revenueEstimator->generateEstimateDto($entity));
        $viewDto->setMassSubscriptionChange($changeDataMapper->createAppDto($entity));
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription/mass-change/{id}/export', name: 'app_app_subscriptions_masschange_exportchange', methods: ['GET'])]
    public function exportChange(
        Request $request,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
        EngineInterface $engine,
    ) {
        $this->getLogger()->info('Received request to export mass subscriptions affected users', ['mass_change_id' => $request->get('id')]);

        try {
            $entity = $massSubscriptionChangeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $exportRequest = new ExportRequest(
            sprintf('mass_change-%s', $entity->getId()),
            'csv',
            MassSubscriptionChangeCustomersDataProvider::class,
            ['mass_change_id' => (string) $entity->getId()]
        );

        $exportResponse = $engine->process($exportRequest);

        $responseConverter = new ResponseConverter();

        return $responseConverter->convert($exportResponse);
    }

    #[Route('/app/subscription/mass-change/{id}/cancel', name: 'app_app_subscriptions_masschange_cancelchange', methods: ['POST'])]
    public function cancelChange(
        Request $request,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
    ) {
        $this->getLogger()->info('Received request to cancel mass subscriptions', ['mass_change_id' => $request->get('id')]);

        try {
            $entity = $massSubscriptionChangeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setStatus(MassSubscriptionChangeStatus::CANCELLED);
        $massSubscriptionChangeRepository->save($entity);

        return new JsonResponse(['status' => MassSubscriptionChangeStatus::CANCELLED->value]);
    }

    #[Route('/app/subscription/mass-change/{id}/uncancel', name: 'app_app_subscriptions_masschange_uncancelchange', methods: ['POST'])]
    public function uncancelChange(
        Request $request,
        MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
    ) {
        $this->getLogger()->info('Received request to uncancel mass subscriptions', ['mass_change_id' => $request->get('id')]);
        try {
            $entity = $massSubscriptionChangeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setStatus(MassSubscriptionChangeStatus::CREATED);
        $massSubscriptionChangeRepository->save($entity);

        return new JsonResponse(['status' => MassSubscriptionChangeStatus::CREATED->value]);
    }
}
