<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\System;

use App\Controller\App\CrudListTrait;
use App\DataMappers\CancellationDataMapper;
use App\Dto\Response\App\System\ViewCancellationRequest;
use App\Repository\CancellationRequestRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CancellationRequestsController
{
    use CrudListTrait;

    #[Route('/app/system/cancellation-request/list', name: 'app_app_system_cancellationrequests_listcancellationrequests', methods: ['GET'])]
    public function listCancellationRequests(
        Request $request,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $cancellationRequestRepository, $serializer, $dataMapper);
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
}
