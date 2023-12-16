<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Workflows;

use App\Controller\App\CrudListTrait;
use App\DataMappers\Workflows\SubscriptionCreationDataMapper;
use App\Dto\Response\App\Workflows\ViewSubscriptionCreation;
use App\Filters\Workflows\CancellationRequestList;
use App\Repository\SubscriptionCreationRepositoryInterface;
use App\Subscription\SubscriptionCreationProcessor;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriptionCreationController
{
    use CrudListTrait;

    #[Route('/app/system/subscription-creation/list', name: 'app_app_workflows_subscriptioncreation_listsubscriptioncreation', methods: ['GET'])]
    public function listSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $subscriptionCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/subscription-creation/{id}/view', name: 'app_app_workflows_subscriptioncreation_viewsubscriptioncreation', methods: ['GET'])]
    public function viewSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        try {
            $entity = $subscriptionCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $viewDto = new ViewSubscriptionCreation();
        $viewDto->setSubscriptionCreation($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/system/subscription-creation/{id}/process', name: 'app_app_workflows_subscriptioncreation_processsubscriptioncreation', methods: ['POST'])]
    public function processSubscriptionCreation(
        Request $request,
        SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        SubscriptionCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
        SubscriptionCreationProcessor $subscriptionCreationProcessor,
    ): Response {
        try {
            $entity = $subscriptionCreationRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionCreationProcessor->process($entity);
        $subscriptionCreationRepository->save($entity);

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
