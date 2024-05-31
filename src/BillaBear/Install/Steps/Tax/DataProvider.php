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
                'states' => [
                    'TX' => [
                        'name' => 'Texas',
                        'threshold' => 50000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.25],
                        ],
                    ],
                    'KS' => [
                        'name' => 'Kansas',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.5],
                        ],
                    ],
                    'NM' => [
                        'name' => 'New Mexico',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4.875],
                        ],
                    ],
                    'AZ' => [
                        'name' => 'Arizona',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5.6],
                        ],
                    ],
                    'CA' => [
                        'name' => 'California',
                        'threshold' => 50000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7.25],
                        ],
                    ],
                    'WA' => [
                        'name' => 'Washington',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.5],
                        ],
                    ],
                    'UT' => [
                        'name' => 'Utah',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4.85],
                        ],
                    ],
                    'WY' => [
                        'name' => 'Wyoming',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4],
                        ],
                    ],
                    'ND' => [
                        'name' => 'North Dakota',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5],
                        ],
                    ],
                    'SD' => [
                        'name' => 'South Dakota',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4.5],
                        ],
                    ],
                    'NE' => [
                        'name' => 'Nebraska',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5.5],
                        ],
                    ],
                    'MN' => [
                        'name' => 'Minnesota',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.88],
                        ],
                    ],
                    'IA' => [
                        'name' => 'Iowa',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'LA' => [
                        'name' => 'Louisiana',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4.45],
                        ],
                    ],
                    'WI' => [
                        'name' => 'Wisconsin',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5],
                        ],
                    ],
                    'MI' => [
                        'name' => 'Michigan',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'IL' => [
                        'name' => 'Illinois',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.25],
                        ],
                    ],
                    'IN' => [
                        'name' => 'Indiana',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'OH' => [
                        'name' => 'Ohio',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5.75],
                        ],
                    ],
                    'PA' => [
                        'name' => 'Pennsylvania',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'ME' => [
                        'name' => 'Maine',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 5.5],
                        ],
                    ],
                    'VT' => [
                        'name' => 'Vermont',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'NY' => [
                        'name' => 'New York',
                        'threshold' => 50000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4],
                        ],
                    ],
                    'NJ' => [
                        'name' => 'New Jersey',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.63],
                        ],
                    ],
                    'MD' => [
                        'name' => 'Maryland',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.63],
                        ],
                    ],
                    'WV' => [
                        'name' => 'West Virginia',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.63],
                        ],
                    ],
                    'KY' => [
                        'name' => 'Kentucky',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'TN' => [
                        'name' => 'Tennessee',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'NC' => [
                        'name' => 'North Carolina',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4.75],
                        ],
                    ],
                    'SC' => [
                        'name' => 'South Carolina',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'AL' => [
                        'name' => 'Alabama',
                        'threshold' => 25000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 4],
                        ],
                    ],
                    'MS' => [
                        'name' => 'Mississippi',
                        'threshold' => 25000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'MA' => [
                        'name' => 'Massachusetts',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6.25],
                        ],
                    ],
                    'RI' => [
                        'name' => 'Rhode Island',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'CT' => [
                        'name' => 'Connecticut',
                        'threshold' => 10000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                ],
            ],
            'CA' => [
                'name' => 'Canada',
                'threshold' => 3000000,
                'in_eu' => false,
                'currency' => 'CAD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 5],
                ],
                'states' => [
                    'QC' => [
                        'name' => 'Quebec',
                        'threshold' => 3000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 9.975],
                        ],
                    ],
                    'BC' => [
                        'name' => 'British Colombia',
                        'threshold' => 1000000,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'SK' => [
                        'name' => 'Saskatchewan',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 6],
                        ],
                    ],
                    'MB' => [
                        'name' => 'Manitoba',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 7],
                        ],
                    ],
                    'ON' => [
                        'name' => 'Ontario',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 8],
                        ],
                    ],
                    'NB' => [
                        'name' => 'New Brunswick',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 10],
                        ],
                    ],
                    'NL' => [
                        'name' => 'Newfoundland and Labrador',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 10],
                        ],
                    ],
                    'NS' => [
                        'name' => 'Nova Scotia',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 10],
                        ],
                    ],
                    'PE' => [
                        'name' => 'Prince Edward Island',
                        'threshold' => 0,
                        'rates' => [
                            'Standard VAT' => ['default' => true, 'rate' => 10],
                        ],
                    ],
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

            // South American

            'MX' => [
                'name' => 'Mexico',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'MXN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'GT' => [
                'name' => 'Guatemala',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'GTQ',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 12],
                ],
            ],
            'NI' => [
                'name' => 'Nicaragua',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'NIO',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'PA' => [
                'name' => 'Panama',
                'threshold' => 3600000,
                'in_eu' => false,
                'currency' => 'PAB',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 7],
                ],
            ],
            'PY' => [
                'name' => 'Paraguay',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'PYG',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                    'Reduce VAT' => ['default' => false, 'rate' => 7],
                ],
            ],
            'PE' => [
                'name' => 'Peru',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'PEN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'DO' => [
                'name' => 'Dominican Republic',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'DOP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'UY' => [
                'name' => 'Uruguay',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'UYU',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 22],
                ],
            ],
            'VE' => [
                'name' => 'Venezuela',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'CR' => [
                'name' => 'Costa Rica',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'CRC',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 13],
                ],
            ],
            'CU' => [
                'name' => 'Cuba',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'CUP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'PR' => [
                'name' => 'Puerto Rico',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10.5],
                ],
            ],
            'CO' => [
                'name' => 'Colombia',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'COP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'EC' => [
                'name' => 'Ecuador',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 12],
                ],
            ],
            'BR' => [
                'name' => 'Brazil',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'BRL',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 12],
                ],
            ],
            'BO' => [
                'name' => 'Bolivia',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'BOB',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 13],
                ],
            ],
            'CL' => [
                'name' => 'Chile',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'CLP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'AR' => [
                'name' => 'Argentina',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'ARS',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 21],
                ],
            ],
            'SR' => [
                'name' => 'Suriname',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'SRD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],

            // Africa

            'ZA' => [
                'name' => 'South Africa',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'ZAR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'LS' => [
                'name' => 'Lesotho',
                'threshold' => 85000000,
                'in_eu' => false,
                'currency' => 'LSL',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'SZ' => [
                'name' => 'Swaziland',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'SZL',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'NA' => [
                'name' => 'Namibia',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'NAD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'BW' => [
                'name' => 'Botswana',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'BWP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 14],
                ],
            ],
            'ZW' => [
                'name' => 'Zimbabwe',
                'threshold' => 2500000,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'MG' => [
                'name' => 'Madagascar',
                'threshold' => 400000000,
                'in_eu' => false,
                'currency' => 'MGA',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'MZ' => [
                'name' => 'Mozambique',
                'threshold' => 250000000,
                'in_eu' => false,
                'currency' => 'MGA',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'AO' => [
                'name' => 'Angola',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'AOA',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 14],
                ],
            ],
            'ZM' => [
                'name' => 'Zambia',
                'threshold' => 80000000,
                'in_eu' => false,
                'currency' => 'ZMW',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'MW' => [
                'name' => 'Malawi',
                'threshold' => 2500000000,
                'in_eu' => false,
                'currency' => 'MWK',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16.5],
                ],
            ],
            'GA' => [
                'name' => 'Gabon',
                'threshold' => 2500000000,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'GQ' => [
                'name' => 'Equatorial Guinea',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'CG' => [
                'name' => 'Republic Of The Congo',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'CD' => [
                'name' => 'Democratic Republic Of The Congo',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'CDF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'RW' => [
                'name' => 'Rwanda',
                'threshold' => 500000000,
                'in_eu' => false,
                'currency' => 'RWF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'BI' => [
                'name' => 'Burundi',
                'threshold' => 10000000,
                'in_eu' => false,
                'currency' => 'BIF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'TZ' => [
                'name' => 'Tanzania',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'TZS',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'UG' => [
                'name' => 'Uganda',
                'threshold' => 15000000000,
                'in_eu' => false,
                'currency' => 'UGX',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'KE' => [
                'name' => 'Kenya',
                'threshold' => 500000000,
                'in_eu' => false,
                'currency' => 'UGX',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 16],
                ],
            ],
            'SO' => [
                'name' => 'Somalia',
                'threshold' => 5000000,
                'in_eu' => false,
                'currency' => 'SOS',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 5],
                ],
            ],
            'CM' => [
                'name' => 'Cameroon',
                'threshold' => 500000000,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19.25],
                ],
            ],
            'CF' => [
                'name' => 'Central African Republic',
                'threshold' => 300000000,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'SS' => [
                'name' => 'South Sudan',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'SSP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'SD' => [
                'name' => 'Sudan',
                'threshold' => 10000000,
                'in_eu' => false,
                'currency' => 'SDG',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 17],
                ],
            ],
            'ET' => [
                'name' => 'Ethiopia',
                'threshold' => 10000000,
                'in_eu' => false,
                'currency' => 'SDG',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'DJ' => [
                'name' => 'Djibouti',
                'threshold' => 500000000,
                'in_eu' => false,
                'currency' => 'DJF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'ER' => [
                'name' => 'Eritrea',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'ERN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 5],
                ],
            ],
            'MA' => [
                'name' => 'Morocco',
                'threshold' => 200000000,
                'in_eu' => false,
                'currency' => 'MAD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'EH' => [
                'name' => 'Western Sahara',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'MAD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 20],
                ],
            ],
            'MR' => [
                'name' => 'Mauritius',
                'threshold' => 15200000,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'SN' => [
                'name' => 'Senegal',
                'threshold' => 15200000,
                'in_eu' => false,
                'currency' => 'USD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'GM' => [
                'name' => 'Gambia',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'GMD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'GW' => [
                'name' => 'Guinea-Bissau',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'XOF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'GN' => [
                'name' => 'Guinea',
                'threshold' => 50000000000,
                'in_eu' => false,
                'currency' => 'GNF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'SL' => [
                'name' => 'Sierra Leone',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'SLL',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'LR' => [
                'name' => 'Liberia',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'LRD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'GH' => [
                'name' => 'Ghana',
                'threshold' => 20000000,
                'in_eu' => false,
                'currency' => 'GHS',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 15],
                ],
            ],
            'TG' => [
                'name' => 'Togo',
                'threshold' => 20000000,
                'in_eu' => false,
                'currency' => 'XOF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'BJ' => [
                'name' => 'Benin',
                'threshold' => 5000000000,
                'in_eu' => false,
                'currency' => 'XOF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'NG' => [
                'name' => 'Nigeria',
                'threshold' => 2500000000,
                'in_eu' => false,
                'currency' => 'NGN',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 7.5],
                ],
            ],
            'NE' => [
                'name' => 'Niger',
                'threshold' => 5000000000,
                'in_eu' => false,
                'currency' => 'XOF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'ML' => [
                'name' => 'Mali',
                'threshold' => 5000000000,
                'in_eu' => false,
                'currency' => 'XOF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'TD' => [
                'name' => 'Chad',
                'threshold' => 50000000000,
                'in_eu' => false,
                'currency' => 'XAF',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'DZ' => [
                'name' => 'Algeria',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'DZD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'TN' => [
                'name' => 'Tunisia',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'TND',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 19],
                ],
            ],
            'LY' => [
                'name' => 'Libya',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'LYD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 0],
                ],
            ],
            'EG' => [
                'name' => 'Egypt',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'EGP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 14],
                ],
            ],

            // Middle East
            'IL' => [
                'name' => 'Israel',
                'threshold' => 0,
                'in_eu' => false,
                'currency' => 'ILS',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 17],
                ],
            ],

            // Asia

            // Pacfic
            'JP' => [
                'name' => 'Japan',
                'threshold' => 1000000000,
                'in_eu' => false,
                'currency' => 'JPY',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'CN' => [
                'name' => 'China',
                'threshold' => 3000000,
                'in_eu' => false,
                'currency' => 'CNY',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 13],
                ],
            ],
            'IN' => [
                'name' => 'India',
                'threshold' => 200000000,
                'in_eu' => false,
                'currency' => 'INR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 18],
                ],
            ],
            'PK' => [
                'name' => 'Pakistan',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'PKR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 17],
                ],
            ],
            'PH' => [
                'name' => 'Philippines',
                'threshold' => 300000000,
                'in_eu' => false,
                'currency' => 'PHP',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 12],
                ],
            ],
            'ID' => [
                'name' => 'Indonesia',
                'threshold' => 480000000000,
                'in_eu' => false,
                'currency' => 'IDR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'MY' => [
                'name' => 'Malaysia',
                'threshold' => 50000000,
                'in_eu' => false,
                'currency' => 'MYR',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],
            'TH' => [
                'name' => 'Thailand',
                'threshold' => 180000000,
                'in_eu' => false,
                'currency' => 'THB',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 7],
                ],
            ],
            'TW' => [
                'name' => 'Taiwan',
                'threshold' => 48000000,
                'in_eu' => false,
                'currency' => 'TWD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 5],
                ],
            ],
            'SG' => [
                'name' => 'Singapore',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'SGD',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 7],
                ],
            ],
            'KR' => [
                'name' => 'South Korea',
                'threshold' => 100000000,
                'in_eu' => false,
                'currency' => 'KRW',
                'rates' => [
                    'Standard VAT' => ['default' => true, 'rate' => 10],
                ],
            ],

            'AU' => [
                'name' => 'Australia',
                'threshold' => 7500000,
                'in_eu' => false,
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
