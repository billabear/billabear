<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Workflows;

use App\Controller\App\CrudListTrait;
use App\DataMappers\Workflows\RefundCreatedProcessDataMapper;
use App\Dto\Response\App\Workflows\ViewRefundCreatedProcess;
use App\Filters\Workflows\CancellationRequestList;
use App\Repository\RefundCreatedProcessRepositoryInterface;
use App\Subscription\SubscriptionCreationProcessor;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RefundCreatedProcessController
{
    use CrudListTrait;

    #[Route('/app/system/refund-created-process/list', name: 'app_app_workflows_refundcreatedprocess_listrefundcreatedprocess', methods: ['GET'])]
    public function listRefundCreatedProcess(
        Request $request,
        RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        RefundCreatedProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $refundCreatedProcessRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/refund-created-process/{id}/view', name: 'app_app_workflows_refundcreatedprocess_viewrefundcreatedprocess', methods: ['GET'])]
    public function viewRefundCreatedProcess(
        Request $request,
        RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        RefundCreatedProcessDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
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
}
