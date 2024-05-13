<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\ChargeBackCreationDataMapper;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewChargeBackCreation;
use BillaBear\Enum\WorkflowType;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Payment\ChargeBackCreationProcessor;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChargeBackCreationController
{
    use CrudListTrait;

    #[Route('/app/workflow/charge-back-creation/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
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

    #[Route('/app/system/charge-back-creation/list', name: 'app_app_workflows_chargebackcreation_listchargebackcreation', methods: ['GET'])]
    public function listChargeBackCreation(
        Request $request,
        ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        ChargeBackCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $chargeBackCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/charge-back-creation/{id}/view', name: 'app_app_workflows_chargebackcreation_viewchargebackcreation', methods: ['GET'])]
    public function viewChargeBackCreation(
        Request $request,
        ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        ChargeBackCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
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
