<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Export\DataProvider;

use App\Repository\TaxReportRepositoryInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\ExportRequest;

class ReceivablesDataProvider implements DataProviderInterface
{
    public function __construct(private TaxReportRepositoryInterface $taxReportRepository)
    {
    }

    public function getData(ExportRequest $exportRequest): iterable
    {
        return $this->taxReportRepository->getReportItems($exportRequest->getDataProviderParameters());
    }
}
