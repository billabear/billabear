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

namespace App\Tax;

use App\Entity\Customer;
use App\Enum\TaxType;

class TaxRateProvider implements TaxRateProviderInterface
{
    public function __construct(private CountryRules $countryRules)
    {
    }

    public function getRateForCustomer(Customer $customer, TaxType $taxType): TaxInfo
    {
        if (TaxType::PHYSICAL === $taxType) {
            return new TaxInfo($this->countryRules->getDigitalVatPercentage($customer->getBrandSettings()->getAddress()), $customer->getBrandSettings()->getAddress()->getCountry());
        }

        return new TaxInfo($this->countryRules->getDigitalVatPercentage($customer->getBillingAddress()), $customer->getBillingAddress()->getCountry());
    }
}
