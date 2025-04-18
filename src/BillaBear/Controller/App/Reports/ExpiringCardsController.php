<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Reports;

use BillaBear\DataMappers\Reports\ExpiringCardsDataMapper;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ExpiringCardsController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/reports/expiring-cards', name: 'app_app_reports_expiringcards_getcards', methods: ['GET'])]
    public function getCards(
        PaymentCardRepositoryInterface $paymentCardRepository,
        ExpiringCardsDataMapper $expiringCardsFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received a request to view expiring cards report');
        $expiringCards = $paymentCardRepository->getExpiringDefaultThisMonth();

        $dtos = array_map([$expiringCardsFactory, 'createAppDto'], $expiringCards);
        $listView = new ListResponse();
        $listView->setData($dtos);
        $listView->setHasMore(false);
        $json = $serializer->serialize($listView, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
