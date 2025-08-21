<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Data;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use Parthenon\Common\Address;

trait CustomerTrait
{
    public function buildCustomer(): Customer
    {
        $brandAddress = new Address();
        $brandAddress->setStreetLineOne('1 BillaBear Way');
        $brandAddress->setStreetLineTwo('Humble Building');
        $brandAddress->setCity('Irvine');
        $brandAddress->setRegion('Ayrshire');
        $brandAddress->setCountry('GB');
        $brandAddress->setPostcode('KA12 9DA');

        $customer = new Customer();
        $customer->setName('Name');
        $customer->setBillingEmail('max.mustermann@example.org');
        $customer->setBrandSettings(new BrandSettings());
        $customer->getBrandSettings()->setBrandName('Dummy Brand');
        $customer->getBrandSettings()->setEmailAddress('test@example.com');
        $customer->getBrandSettings()->setAddress($brandAddress);

        return $customer;
    }
}
