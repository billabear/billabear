<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\CancellationDataMapper;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewCancellationRequest;
use BillaBear\Enum\WorkflowType;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Subscription\CancellationRequestProcessor;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedCancellationRequests;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class CancellationRequestsController
{
    use CrudListTrait;

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/workflow/cancellation-request/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $places = $placesProvider->getPlacesForWorkflow(WorkflowType::CANCEL_SUBSCRIPTION);
        $eventHandlers = $dynamicHandlerProvider->getAll();

        $placesDto = array_map([$placeDataMapper, 'createAppDto'], $places);
        $eventHandlers = array_map([$eventHandlerDataMapper, 'createAppDto'], $eventHandlers);

        $dto = new EditWorkflow();
        $dto->setPlaces($placesDto);
        $dto->setTransitionHandlers($eventHandlers);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/cancellation-request/bulk', name: 'billabear_app_workflows_cancellationrequests_bulkprocessfailed', methods: ['POST'])]
    public function bulkProcessFailed(MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new ReprocessFailedCancellationRequests());

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/app/system/cancellation-request/list', name: 'app_app_system_cancellationrequests_listcancellationrequests', methods: ['GET'])]
    public function listCancellationRequests(
        Request $request,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $cancellationRequestRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/cancellation-request/{id}/view', name: 'app_app_system_cancellationrequests_viewcancellationrequests', methods: ['GET'])]
    public function viewCancellationRequest(
        Request $request,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        try {
            $entity = $cancellationRequestRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewCancellationRequest();
        $viewDto->setCancellationRequest($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/cancellation-request/{id}/process', name: 'app_app_system_cancellationrequests_processcancellationrequests', methods: ['POST'])]
    public function processCancellationRequest(
        Request $request,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $dataMapper,
        SerializerInterface $serializer,
        CancellationRequestProcessor $cancellationRequestProcessor,
    ): Response {
        try {
            $entity = $cancellationRequestRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $cancellationRequestProcessor->process($entity);
        $cancellationRequestRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
