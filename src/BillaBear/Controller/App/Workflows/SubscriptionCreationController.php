<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\SubscriptionCreationDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewSubscriptionCreation;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Repository\SubscriptionCreationRepositoryInterface;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedSubscriptionCreated;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriptionCreationController
{
    use CrudListTrait;
    use LoggerAwareTrait;

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/workflow/subscription-creation/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view subscription creation workflow');

        $places = $placesProvider->getPlacesForWorkflow(WorkflowType::CREATE_SUBSCRIPTION);
        $eventHandlers = $dynamicHandlerProvider->getAll();

        $placesDto = array_map([$placeDataMapper, 'createAppDto'], $places);
        $eventHandlers = array_map([$eventHandlerDataMapper, 'createAppDto'], $eventHandlers);

        $dto = new EditWorkflow();
        $dto->setPlaces($placesDto);
        $dto->setTransitionHandlers($eventHandlers);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/subscription-creation/list', name: 'app_app_workflows_subscriptioncreation_listsubscriptioncreation', methods: ['GET'])]
    public function listSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view list subscription creation');

        return $this->crudList($request, $subscriptionCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/subscription-creation/bulk', name: 'billabear_app_workflows_subscriptioncreation_bulkprocessfailed', methods: ['POST'])]
    public function bulkProcessFailed(MessageBusInterface $messageBus): JsonResponse
    {
        $this->getLogger()->info('Received request to start bulk process subscription creation workflow');

        $messageBus->dispatch(new ReprocessFailedSubscriptionCreated());

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/app/system/subscription-creation/{id}/view', name: 'app_app_workflows_subscriptioncreation_viewsubscriptioncreation', methods: ['GET'])]
    public function viewSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view subscription creation', ['subscription_creation_id' => $request->get('id')]);

        try {
            $entity = $subscriptionCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewSubscriptionCreation();
        $viewDto->setSubscriptionCreation($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/subscription-creation/{id}/process', name: 'app_app_workflows_subscriptioncreation_processsubscriptioncreation', methods: ['POST'])]
    public function processSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
        SubscriptionCreationProcessor $subscriptionCreationProcessor,
    ): Response {
        $this->getLogger()->info('Received request to process subscription creation', ['subscription_creation_id' => $request->get('id')]);

        try {
            $entity = $subscriptionCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionCreationProcessor->process($entity);
        $subscriptionCreationRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
