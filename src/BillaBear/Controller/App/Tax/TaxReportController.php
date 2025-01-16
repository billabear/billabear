<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Dto\Response\App\Tax\TaxReportDashboard;
use BillaBear\Export\DataProvider\TaxReportDataProvider;
use BillaBear\Export\Response\ResponseConverter;
use BillaBear\Repository\TaxReportRepositoryInterface;
use BillaBear\Tax\Report\ActiveCountryProvider;
use BillaBear\Tax\Report\ReportItemBuilder;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Export\Engine\EngineInterface;
use Parthenon\Export\ExportRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaxReportController
{
    use LoggerAwareTrait;

    #[Route('/app/tax/report', name: 'app_app_tax_taxreport_viewlist', methods: ['GET'])]
    public function viewReport(
        Request $request,
        TaxReportRepositoryInterface $taxReportRepository,
        ReportItemBuilder $reportItemBuilder,
        ActiveCountryProvider $activeCountryProvider,
        SerializerInterface $serializer,
    ): JsonResponse {
        $this->getLogger()->info('Received request to view report');

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
        $list = new TaxReportDashboard();
        $list->setLatestTaxItems(iterator_to_array($rawData));
        $list->setActiveCountries($activeCountryProvider->getActiveCountries());

        $json = $serializer->serialize($list, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/tax/report/export', name: 'billabear_app_tax_taxreport_exportchange', methods: ['GET'])]
    public function exportReport(
        Request $request,
        EngineInterface $engine,
    ) {
        $this->getLogger()->info('Received request to export report');
        $exportRequest = new ExportRequest(
            sprintf('tax_report'),
            'csv',
            TaxReportDataProvider::class,
            []
        );

        $exportResponse = $engine->process($exportRequest);

        $responseConverter = new ResponseConverter();

        return $responseConverter->convert($exportResponse);
    }
}
