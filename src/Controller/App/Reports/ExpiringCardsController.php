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

namespace App\Controller\App\Reports;

use App\Dto\Response\App\ListResponse;
use App\Factory\Reports\ExpiringCardsFactory;
use App\Repository\PaymentCardRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ExpiringCardsController
{
    #[Route('/app/reports/expiring-cards', name: 'app_app_reports_expiringcards_getcards', methods: ['GET'])]
    public function getCards(
        PaymentCardRepositoryInterface $paymentCardRepository,
        ExpiringCardsFactory $expiringCardsFactory,
        SerializerInterface $serializer,
    ): Response {
        $expiringCards = $paymentCardRepository->getExpiringDefaultThisMonth();

        $dtos = array_map([$expiringCardsFactory, 'createAppDto'], $expiringCards);
        $listView = new ListResponse();
        $listView->setData($dtos);
        $listView->setHasMore(false);
        $json = $serializer->serialize($listView, 'json');

        return new JsonResponse($json, json: true);
    }
}
