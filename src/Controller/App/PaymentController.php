<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Api\Filters\PaymentList;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Payment\PaymentView;
use App\Factory\PaymentFactory;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentController
{
    #[Route('/app/payments', name: 'app_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        SerializerInterface $serializer,
        PaymentFactory $paymentFactory,
    ): Response {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new PaymentList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $paymentRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$paymentFactory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/payments/{id}', name: 'app_payment_view', methods: ['GET'])]
    public function viewPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        PaymentFactory $paymentFactory,
        SerializerInterface $serializer,
    ) {
        try {
            $payment = $paymentRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        $view = new PaymentView();
        $view->setPayment($paymentFactory->createAppDto($payment));
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }
}
