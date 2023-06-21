<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
