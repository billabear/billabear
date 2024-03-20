<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Workflows;

use App\Controller\App\CrudListTrait;
use App\DataMappers\Workflows\PaymentFailureProcessDataMapper;
use App\Dto\Response\App\Workflows\ViewPaymentFailureProcess;
use App\Repository\PaymentFailureProcessRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentFailureProcessController
{
    use CrudListTrait;

    #[Route('/app/system/payment-failure-process/list', name: 'app_app_workflows_paymentfailureprocess_listpaymentfailureprocess', methods: ['GET'])]
    public function listPaymentFailureProcess(
        Request $request,
        PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        PaymentFailureProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $paymentFailureProcessRepository, $serializer, $dataMapper);
    }

    #[Route('/app/system/payment-failure-process/{id}/view', name: 'app_app_workflows_paymentfailureprocess_viewpaymentfailureprocess', methods: ['GET'])]
    public function viewPaymentFailureProcess(
        Request $request,
        PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        PaymentFailureProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
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
