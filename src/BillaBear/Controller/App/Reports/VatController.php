<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Reports;

use BillaBear\Repository\VatReportRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VatController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/reports/vat', name: 'app_app_reports_vat_getvat', methods: ['GET'])]
    public function getVat(
        VatReportRepositoryInterface $repository,
    ): Response {
        $this->getLogger()->info('Received a request to view VAT report');
        $vat = $repository->getDataForMonth(new \DateTime('now'));
        $data = ['vat' => $vat];

        return new JsonResponse($data);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
