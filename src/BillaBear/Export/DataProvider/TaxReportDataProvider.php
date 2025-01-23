<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Export\DataProvider;

use BillaBear\Repository\TaxReportRepositoryInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\ExportRequest;

class TaxReportDataProvider implements DataProviderInterface
{
    public function __construct(private TaxReportRepositoryInterface $taxReportRepository)
    {
    }

    public function getData(ExportRequest $exportRequest): iterable
    {
        return $this->taxReportRepository->getReportItems($exportRequest->getDataProviderParameters());
    }
}
