<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tax\DataProviders;

use Parthenon\Common\Address;
use TaxJar\Client;

class TaxJarDataProvider implements TaxRateProviderInterface
{
    public function __construct(private Client $taxJar)
    {
    }

    public function getRateForAddress(Address $address, string $taxType): float
    {
        $response = $this->taxJar->ratesForLocation($address->getPostcode(), ['city' => $address->getCity(), 'country' => $address->getCountry()]);

        return (float) $response->combined_rate;
    }

    public function isVatNumberValid(string $vatNumber): bool
    {
        return $this->taxJar->validate(['vat' => $vatNumber])->valid;
    }

    public function getRateTypes(): array
    {
        // TODO: Implement getRateTypes() method.
    }
}
