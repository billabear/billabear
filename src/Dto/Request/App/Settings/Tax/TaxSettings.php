<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Settings\Tax;

use Symfony\Component\Serializer\Annotation\SerializedName;

class TaxSettings
{
    #[SerializedName('tax_customers_with_tax_number')]
    private $taxCustomersWithTaxNumber;

    public function getTaxCustomersWithTaxNumber()
    {
        return $this->taxCustomersWithTaxNumber;
    }

    public function setTaxCustomersWithTaxNumber($taxCustomersWithTaxNumber): void
    {
        $this->taxCustomersWithTaxNumber = $taxCustomersWithTaxNumber;
    }
}
