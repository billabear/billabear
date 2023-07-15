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
use App\Repository\SettingsRepositoryInterface;

class IgnoreCustomerTax implements TaxRateProviderInterface
{
    public function __construct(private TaxRateProviderInterface $taxRateProvider, private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function getRateForCustomer(Customer $customer): float
    {
        if ($customer->getStandardTaxRate()) {
            return $customer->getStandardTaxRate();
        }
        $taxCustomersWithTaxNumbers = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getTaxCustomersWithTaxNumbers();

        if (!$taxCustomersWithTaxNumbers && $customer->getTaxNumber()) {
            return 0.0;
        }

        return $this->taxRateProvider->getRateForCustomer($customer);
    }
}
