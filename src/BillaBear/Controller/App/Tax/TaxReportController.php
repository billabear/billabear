<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Dto\Response\App\Tax\TaxReportDashboard;
use BillaBear\Repository\TaxReportRepositoryInterface;
use BillaBear\Tax\Report\ActiveCountryProvider;
use BillaBear\Tax\Report\ReportItemBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaxReportController
{
    #[Route('/app/tax/report', name: 'app_app_tax_taxreport_viewlist', methods: ['GET'])]
    public function viewReport(
        Request $request,
        TaxReportRepositoryInterface $taxReportRepository,
        ReportItemBuilder $reportItemBuilder,
        ActiveCountryProvider $activeCountryProvider,
        SerializerInterface $serializer,
    ): JsonResponse {
        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'reason' => 'limit is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'reason' => 'limit is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
        // TODO add filters
        $filters = [];

        $rawData = $taxReportRepository->getReportItems($filters, $resultsPerPage, 0);
        $dtos = array_map([$reportItemBuilder, 'buildItem'], iterator_to_array($rawData));

        $list = new TaxReportDashboard();
        $list->setLatestTaxItems($dtos);
        $list->setActiveCountries($activeCountryProvider->getActiveCountries());

        $json = $serializer->serialize($list, 'json');

        return new JsonResponse($json, json: true);
    }
}
