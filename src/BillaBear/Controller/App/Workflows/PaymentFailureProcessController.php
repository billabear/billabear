<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\DataMappers\Workflows\PaymentFailureProcessDataMapper;
use BillaBear\Dto\Response\App\Workflows\ViewPaymentFailureProcess;
use BillaBear\Repository\PaymentFailureProcessRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentFailureProcessController
{
    use CrudListTrait;
    use LoggerAwareTrait;

    #[Route('/app/system/payment-failure-process/list', name: 'app_app_workflows_paymentfailureprocess_listpaymentfailureprocess', methods: ['GET'])]
    public function listPaymentFailureProcess(
        Request $request,
        PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        PaymentFailureProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list payment failure process');

        return $this->crudList($request, $paymentFailureProcessRepository, $serializer, $dataMapper);
    }

    #[Route('/app/system/payment-failure-process/{id}/view', name: 'app_app_workflows_paymentfailureprocess_viewpaymentfailureprocess', methods: ['GET'])]
    public function viewPaymentFailureProcess(
        Request $request,
        PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        PaymentFailureProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view payment failure process', ['payment_failure_process_id' => $request->get('id')]);

        try {
            $entity = $paymentFailureProcessRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewPaymentFailureProcess();
        $viewDto->setPaymentFailureProcess($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/payment-failure-process/{id}/process', name: 'app_app_workflows_paymentfailureprocess_processpaymentfailureprocess', methods: ['POST'])]
    public function processPaymentFailureProcess(
        Request $request,
        PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        PaymentFailureProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to process payment failure process', ['payment_failure_process_id' => $request->get('id')]);

        try {
            $entity = $paymentFailureProcessRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
