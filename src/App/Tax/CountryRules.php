<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tax;

use App\Exception\NoRateForCountryException;
use Parthenon\Common\Address;

class CountryRules
{
    protected array $europeanEconomicArea = [
        'AT' => 20,
        'BE' => 21,
        'BG' => 20,
        'HR' => 25,
        'CY' => 19,
        'CZ' => 21,
        'DK' => 25,
        'EE' => 20,
        'FI' => 24,
        'FR' => 20,
        'DE' => 19,
        'GR' => 24,
        'HU' => 27,
        'IE' => 23,
        'IT' => 22,
        'LV' => 21,
        'LT' => 21,
        'LU' => 17,
        'MT' => 18,
        'NL' => 21,
        'PL' => 23,
        'PT' => 23,
        'RO' => 19,
        'SK' => 20,
        'SI' => 22,
        'ES' => 21,
        'SE' => 25,

        // EEA
        'CH' => 7.7,
        'NO' => 25,
        'IS' => 24,
        'LI' => 7.7,
    ];

    protected array $rates = [
        // UK
        'GB' => 20,
        // North America
        'US' => 0,
        'CA' => 5,
        'MX' => 16,

        // Down under
        'AU' => 18,
        'NZ' => 15,

        // Russia
        'RU' => 20,
        'CN' => 13,
    ];

    public function inEu(Address $address)
    {
        return array_key_exists($address->getCountry(), $this->europeanEconomicArea);
    }

    public function getDigitalVatPercentage(Address $address): float
    {
        $rates = array_merge($this->europeanEconomicArea, $this->rates);

        if (!array_key_exists($address->getCountry(), $rates)) {
            throw new NoRateForCountryException(sprintf('Unable to find a tax rate for %s', $address->getCountry()));
        }

        return floatval($rates[$address->getCountry()]);
    }
}
