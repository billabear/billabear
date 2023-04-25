<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dummy\Data;

use App\Entity\Customer;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Common\Address;

class ReceiptProvider
{
    public function getDummyReceipt(): Receipt
    {
        $customer = new Customer();
        $customer->setName('Name');
        $customer->setBillingEmail('max.mustermann@example.org');

        $receipt = new Receipt();
        $receipt->setCreatedAt(new \DateTime('now'));
        $receipt->setCustomer($customer);

        $lineOne = new ReceiptLine();
        $lineOne->setReceipt($receipt);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setVatTotal(2000);
        $lineOne->setDescription('Example Line One');

        $lineTwo = new ReceiptLine();
        $lineTwo->setReceipt($receipt);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setVatTotal(4000);
        $lineTwo->setDescription('Example Line Two');

        $receipt->setLines([$lineOne, $lineTwo]);
        $receipt->setTotal(30000);
        $receipt->setSubTotal(24000);
        $receipt->setVatTotal(6000);
        $receipt->setCurrency('EUR');
        $receipt->setValid(true);

        $payeeAddress = new Address();
        $payeeAddress->setCompanyName('Company One');
        $payeeAddress->setStreetLineOne('One Example Strasse');
        $payeeAddress->setRegion('Berlin');
        $payeeAddress->setCity('Berlin');
        $payeeAddress->setCountry('Germany');
        $payeeAddress->setPostcode('10366');

        $receipt->setPayeeAddress($payeeAddress);

        $billerAddress = new Address();
        $billerAddress->setCompanyName('Company One');
        $billerAddress->setStreetLineOne('Two Example Straße');
        $billerAddress->setRegion('Berlin');
        $billerAddress->setCity('Berlin');
        $billerAddress->setCountry('Germany');
        $billerAddress->setPostcode('10366');

        $receipt->setBillerAddress($billerAddress);

        $receipt->setInvoiceNumber('SKDLSk');

        return $receipt;
    }
}
