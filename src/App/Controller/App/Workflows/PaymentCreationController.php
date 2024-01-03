<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Workflows;

use App\Controller\App\CrudListTrait;
use App\DataMappers\Workflows\PaymentCreationDataMapper;
use App\Dto\Response\App\Workflows\ViewPaymentCreation;
use App\Filters\Workflows\CancellationRequestList;
use App\Payment\PaymentCreationProcessor;
use App\Repository\PaymentCreationRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentCreationController
{
    use CrudListTrait;

    #[Route('/app/system/payment-creation/list', name: 'app_app_workflows_paymentcreation_listpaymentcreation', methods: ['GET'])]
    public function listPaymentCreation(
        Request $request,
        PaymentCreationRepositoryInterface $paymentCreationRepository,
        PaymentCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $paymentCreationRepository, $serializer, $dataMapper, filterList: new CancellationRequestList());
    }

    #[Route('/app/system/payment-creation/{id}/view', name: 'app_app_workflows_paymentcreation_viewpaymentcreation', methods: ['GET'])]
    public function viewPaymentCreation(
        Request $request,
        PaymentCreationRepositoryInterface $paymentCreationRepository,
        PaymentCreationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
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
