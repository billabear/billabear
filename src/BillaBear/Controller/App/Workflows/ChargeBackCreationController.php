<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\ChargeBackCreationDataMapper;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewChargeBackCreation;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Payment\ChargeBackCreationProcessor;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedPaymentCreation;
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

class ChargeBackCreationController
{
    use CrudListTrait;
    use LoggerAwareTrait;

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/workflow/charge-back-creation/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view charge back creation workflow');
        $places = $placesProvider->getPlacesForWorkflow(WorkflowType::CREATE_CHARGEBACK);
        $eventHandlers = $dynamicHandlerProvider->getAll();

        $placesDto = array_map([$placeDataMapper, 'createAppDto'], $places);
        $eventHandlers = array_map([$eventHandlerDataMapper, 'createAppDto'], $eventHandlers);

        $dto = new EditWorkflow();
        $dto->setPlaces($placesDto);
        $dto->setTransitionHandlers($eventHandlers);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/charge-back-creation/bulk', name: 'billabear_app_workflows_chargebackcreation_bulkprocessfailed', methods: ['POST'])]
    public function bulkProcessFailed(MessageBusInterface $messageBus): JsonResponse
    {
        $this->getLogger()->info('Received request to start bulk process charge back creation');
        $messageBus->dispatch(new ReprocessFailedPaymentCreation());

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/app/system/charge-back-creation/list', name: 'app_app_workflows_chargebackcreation_listchargebackcreation', methods: ['GET'])]
    public function listChargeBackCreation(
        Request $request,
        ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        ChargeBackCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list charge back creation');

        return $this->crudList($request, $chargeBackCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/charge-back-creation/{id}/view', name: 'app_app_workflows_chargebackcreation_viewchargebackcreation', methods: ['GET'])]
    public function viewChargeBackCreation(
        Request $request,
        ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        ChargeBackCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view charge back creation', ['charge_back_id' => $request->attributes->get('id')]);
        try {
            $entity = $chargeBackCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewChargeBackCreation();
        $viewDto->setChargeBackCreation($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/charge-back-creation/{id}/process', name: 'app_app_workflows_chargebackcreation_processchargebackcreation', methods: ['POST'])]
    public function processChargeBackCreation(
        Request $request,
        ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        ChargeBackCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
        ChargeBackCreationProcessor $chargeBackCreationProcessor,
    ): Response {
        $this->getLogger()->info('Received request to process charge back creation', ['charge_back_id' => $request->get('id')]);
        try {
            $entity = $chargeBackCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $chargeBackCreationProcessor->process($entity);
        $chargeBackCreationRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
