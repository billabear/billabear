<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Reports;

use App\DataMappers\Reports\ExpiringCardsDataMapper;
use App\Dto\Response\App\ListResponse;
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
        ExpiringCardsDataMapper $expiringCardsFactory,
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
