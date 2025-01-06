<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\PaymentCreationDataMapper;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\DataMappers\Workflows\TransitionHandlerDataMapper;
use BillaBear\Dto\Generic\App\Workflows\EditWorkflow;
use BillaBear\Dto\Response\App\Workflows\ViewPaymentCreation;
use BillaBear\Filters\Workflows\CancellationRequestList;
use BillaBear\Payment\PaymentCreationProcessor;
use BillaBear\Repository\PaymentCreationRepositoryInterface;
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

class PaymentCreationController
{
    use CrudListTrait;
    use LoggerAwareTrait;

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/workflow/payment-creation/edit', methods: ['GET'])]
    public function viewEdit(
        Request $request,
        PlacesProvider $placesProvider,
        DynamicTransitionHandlerProvider $dynamicHandlerProvider,
        PlaceDataMapper $placeDataMapper,
        TransitionHandlerDataMapper $eventHandlerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view payment creation workflow');

        $places = $placesProvider->getPlacesForWorkflow(WorkflowType::CREATE_PAYMENT);
        $eventHandlers = $dynamicHandlerProvider->getAll();

        $placesDto = array_map([$placeDataMapper, 'createAppDto'], $places);
        $eventHandlers = array_map([$eventHandlerDataMapper, 'createAppDto'], $eventHandlers);

        $dto = new EditWorkflow();
        $dto->setPlaces($placesDto);
        $dto->setTransitionHandlers($eventHandlers);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/payment-creation/bulk', name: 'billabear_app_workflows_paymentcreation_bulkprocessfailed', methods: ['POST'])]
    public function bulkProcessFailed(MessageBusInterface $messageBus): JsonResponse
    {
        $this->getLogger()->info('Received request to start bulk process of payment creation');
        $messageBus->dispatch(new ReprocessFailedPaymentCreation());

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/app/system/payment-creation/list', name: 'app_app_workflows_paymentcreation_listpaymentcreation', methods: ['GET'])]
    public function listPaymentCreation(
        Request $request,
        PaymentCreationRepositoryInterface $paymentCreationRepository,
        PaymentCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list payment creation');

        return $this->crudList($request, $paymentCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/payment-creation/{id}/view', name: 'app_app_workflows_paymentcreation_viewpaymentcreation', methods: ['GET'])]
    public function viewPaymentCreation(
        Request $request,
        PaymentCreationRepositoryInterface $paymentCreationRepository,
        PaymentCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view payment creation', ['payment_creation_id' => $request->get('id')]);

        try {
            $entity = $paymentCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewPaymentCreation();
        $viewDto->setPaymentCreation($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/payment-creation/{id}/process', name: 'app_app_workflows_paymentcreation_processpaymentcreation', methods: ['POST'])]
    public function processPaymentCreation(
        Request $request,
        PaymentCreationRepositoryInterface $paymentCreationRepository,
        PaymentCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
        PaymentCreationProcessor $paymentCreationProcessor,
    ): Response {
        $this->getLogger()->info('Received request to process payment creation', ['payment_creation_id' => $request->get('id')]);

        try {
            $entity = $paymentCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $paymentCreationProcessor->process($entity);
        $paymentCreationRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
