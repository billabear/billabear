<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Steps\Tax;

class DataProvider
{
    public function getCountryList(): \Generator
    {
        $countries = [
            // North America
            'US' => [
                'name' => 'United States',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [],
            ],
            'CA' => [
                'name' => 'Canada',
                'threshold' => 3000000,
                'in_eu' => false,
                'currency' => 'CAD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 5],
                ],
            ],

            // Europe but not EU
            'GB' => [
                'name' => 'United Kingdom',
                'threshold' => 9000000,
                'in_eu' => false,
                'currency' => 'GBP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],

            // EU or EEA

            'EU' => [
                'name' => 'EU One Stop Shop',
                'threshold' => 1000000,
                'in_eu' => false,
                'currency' => 'GBP',
                'rates' => [],
            ],

            'AT' => [
                'name' => 'Austria',
                'threshold' => 3500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'BE' => [
                'name' => 'Belgium',
                'threshold' => 2500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'BG' => [
                'name' => 'Bulgaria',
                'threshold' => 10000000,
                'in_eu' => true,
                'currency' => 'BGN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'HR' => [
                'name' => 'Croatia',
                'threshold' => 4000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 25],
                ],
            ],
            'CY' => [
                'name' => 'Cyprus',
                'threshold' => 1560000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'CZ' => [
                'name' => 'Czechia',
                'threshold' => 200000000,
                'in_eu' => true,
                'currency' => 'CZK',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'DK' => [
                'name' => 'Denmark',
                'threshold' => 5000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 25],
                ],
            ],
            'EE' => [
                'name' => 'Estonia',
                'threshold' => 4000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'FI' => [
                'name' => 'Finland',
                'threshold' => 1500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 24],
                ],
            ],
            'FR' => [
                'name' => 'France',
                'threshold' => 3440000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'DE' => [
                'name' => 'Germany',
                'threshold' => 2200000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'GR' => [
                'name' => 'Greece',
                'threshold' => 0,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 24],
                ],
            ],
            'HU' => [
                'name' => 'Hungary',
                'threshold' => 1200000000,
                'in_eu' => true,
                'currency' => 'HUF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 27],
                ],
            ],
            'IE' => [
                'name' => 'Ireland',
                'threshold' => 4000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 23],
                ],
            ],
            'IT' => [
                'name' => 'Italy',
                'threshold' => 8500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 22],
                ],
            ],
            'LV' => [
                'name' => 'Latvia',
                'threshold' => 5000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'LT' => [
                'name' => 'Lithuania',
                'threshold' => 5500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'LU' => [
                'name' => 'Luxembourg',
                'threshold' => 3500000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 17],
                ],
            ],
            'MT' => [
                'name' => 'Malta',
                'threshold' => 3000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'NL' => [
                'name' => 'Netherlands',
                'threshold' => 2000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'PL' => [
                'name' => 'Poland',
                'threshold' => 20000000,
                'in_eu' => true,
                'currency' => 'PLN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 23],
                ],
            ],
            'PT' => [
                'name' => 'Portugal',
                'threshold' => 0,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 23],
                ],
            ],
            'RO' => [
                'name' => 'Romania',
                'threshold' => 44550000,
                'in_eu' => true,
                'currency' => 'RON',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'SK' => [
                'name' => 'Slovakia',
                'threshold' => 4975000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'SI' => [
                'name' => 'Slovenia',
                'threshold' => 5000000,
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 22],
                ],
            ],
            'ES' => [
                'name' => 'Spain',
                'threshold' => 0, // For reals
                'in_eu' => true,
                'currency' => 'EUR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'SE' => [
                'name' => 'Sweden',
                'threshold' => 32000000,
                'in_eu' => true,
                'currency' => 'SEK',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 25],
                ],
            ],

            'CH' => [
                'name' => 'Switzerland',
                'threshold' => 10000000,
                'in_eu' => true,
                'currency' => 'CHF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 25],
                ],
            ],
            'NO' => [
                'name' => 'Norway',
                'threshold' => 5000000,
                'in_eu' => true,
                'currency' => 'NOK',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 25],
                ],
            ],
            'IS' => [
                'name' => 'Iceland',
                'threshold' => 200000000,
                'in_eu' => true,
                'currency' => 'ISK',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 24],
                ],
            ],
            'LI' => [
                'name' => 'Liechtenstein',
                'threshold' => 10000000,
                'in_eu' => true,
                'currency' => 'CHF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 7.7],
                ],
            ],

            'TR' => [
                'name' => 'Turkey',
                'threshold' => 0,
                'in_eu' => true,
                'currency' => 'TRY',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],

            // Pacfic

            'AU' => [
                'name' => 'Australia',
                'threshold' => 7500000,
                'in_eu' => true,
                'currency' => 'AUD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'NZ' => [
                'name' => 'New Zealand',
                'threshold' => 6000000,
                'in_eu' => true,
                'currency' => 'NZD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
        ];

        foreach ($countries as $code => $country) {
            $country['code'] = $code;
            yield $country;
        }
    }
}
