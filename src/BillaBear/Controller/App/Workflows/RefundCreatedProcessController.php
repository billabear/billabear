<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\RefundCreatedProcessDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewRefundCreatedProcess;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Repository\RefundCreatedProcessRepositoryInterface;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedRefundCreation;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class RefundCreatedProcessController
{
    use CrudListTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/workflow/refund-created-process/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view payment failure process workflow');

        $places = $placesProvider->getPlacesForWorkflow(WorkflowType::CREATE_REFUND);
        $eventHandlers = $dynamicHandlerProvider->getAll();

        $placesDto = array_map([$placeDataMapper, 'createAppDto'], $places);
        $eventHandlers = array_map([$eventHandlerDataMapper, 'createAppDto'], $eventHandlers);

        $dto = new EditWorkflow();
        $dto->setPlaces($placesDto);
        $dto->setTransitionHandlers($eventHandlers);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/refund-created-process/bulk', name: 'billabear_app_workflows_refundcreatedprocess_bulkprocessfailed', methods: ['POST'])]
    public function bulkProcessFailed(MessageBusInterface $messageBus): JsonResponse
    {
        $this->getLogger()->info('Received request to start bulk process refund created process');

        $messageBus->dispatch(new ReprocessFailedRefundCreation());

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/app/system/refund-created-process/list', name: 'app_app_workflows_refundcreatedprocess_listrefundcreatedprocess', methods: ['GET'])]
    public function listRefundCreatedProcess(
        Request $request,
        RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        RefundCreatedProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list refund created process');

        return $this->crudList($request, $refundCreatedProcessRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/refund-created-process/{id}/view', name: 'app_app_workflows_refundcreatedprocess_viewrefundcreatedprocess', methods: ['GET'])]
    public function viewRefundCreatedProcess(
        Request $request,
        RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        RefundCreatedProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to process refund created process', ['refund_created_process_id' => $request->get('id')]);
        try {
            $entity = $refundCreatedProcessRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewRefundCreatedProcess();
        $viewDto->setRefundCreatedProcess($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/refund-created-process/{id}/process', name: 'app_app_workflows_refundcreatedprocess_processrefundcreatedprocess', methods: ['POST'])]
    public function processRefundCreatedProcess(
        Request $request,
        RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        RefundCreatedProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
        SubscriptionCreationProcessor $subscriptionCreationProcessor,
    ): Response {
        $this->getLogger()->info('Received request to view refund created process', ['refund_created_process_id' => $request->get('id')]);

        try {
            $entity = $refundCreatedProcessRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionCreationProcessor->process($entity);
        $refundCreatedProcessRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
