<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Tax;

use Symfony\Component\Serializer\Attribute\SerializedName;

class TaxReportDashboard
{
    #[SerializedName('latest_tax_items')]
    private array $latestTaxItems = [];

    public function getLatestTaxItems(): array
    {
        return $this->latestTaxItems;
    }

    public function setLatestTaxItems(array $latestTaxItems): void
    {
        $this->latestTaxItems = $latestTaxItems;
    }
}
