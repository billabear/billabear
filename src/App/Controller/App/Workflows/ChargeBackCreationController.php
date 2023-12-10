<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Workflows;

use App\Controller\App\CrudListTrait;
use App\DataMappers\Workflows\ChargeBackCreationDataMapper;
use App\Dto\Response\App\Workflows\ViewChargeBackCreation;
use App\Filters\Workflows\CancellationRequestList;
use App\Payment\ChargeBackCreationProcessor;
use App\Repository\ChargeBackCreationRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChargeBackCreationController
{
    use CrudListTrait;

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
