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

namespace App\Tests\Unit\Tax\DataProviders;

use App\Tax\DataProviders\TaxJarDataProvider;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;
use TaxJar\Client;

class TaxJarDataProviderTest extends TestCase
{
    public function testGetRateForAddress()
    {
        $postCode = 'KA12 9HA';
        $country = 'GB';

        $address = new Address();
        $address->setCity('');
        $address->setCountry($country);
        $address->setPostcode($postCode);

        $taxJar = $this->createMock(Client::class);

        $response = new \stdClass();
        $response->combined_rate = '0.0975';

        $taxJar->method('ratesForLocation')->with($postCode, ['city' => '', 'country' => $country])->willReturn($response);

        $subject = new TaxJarDataProvider($taxJar);
        $actual = $subject->getRateForAddress($address, 'fdf');
        $this->assertEquals('0.0975', $actual);
    }

    public function testValidateVatNumber()
    {
        $taxJar = $this->createMock(Client::class);
        $vatNumber = 'Lildd';
        $response = new \stdClass();
        $response->valid = true;
        $taxJar->method('validate')->with(['vat' => $vatNumber])->willReturn($response);

        $subject = new TaxJarDataProvider($taxJar);
        $actual = $subject->isVatNumberValid($vatNumber);
        $this->assertEquals(true, $actual);
    }
}
