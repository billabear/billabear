<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Reports;

use App\Repository\VatReportRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VatController
{
    #[Route('/app/reports/vat', name: 'app_app_reports_vat_getvat', methods: ['GET'])]
    public function getVat(
        VatReportRepositoryInterface $repository,
    ): Response {
        $vat = $repository->getDataForMonth(new \DateTime('now'));
        $data = ['vat' => $vat];

        return new JsonResponse($data);
    }
}
