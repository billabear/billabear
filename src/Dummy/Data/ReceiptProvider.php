<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use Doctrine\Common\Collections\ArrayCollection;
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
        $customer->setBrandSettings(new BrandSettings());
        $customer->getBrandSettings()->setBrandName('Dummy Brand');
        $customer->getBrandSettings()->setAddress(new Address());

        $receipt = new Receipt();
        $receipt->setCreatedAt(new \DateTime('now'));
        $receipt->setCustomer($customer);

        $lineOne = new ReceiptLine();
        $lineOne->setReceipt($receipt);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setVatTotal(2000);
        $lineOne->setVatPercentage(20);
        $lineOne->setDescription('Example Line One');

        $lineTwo = new ReceiptLine();
        $lineTwo->setReceipt($receipt);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setVatTotal(4000);
        $lineTwo->setVatPercentage(20);
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

    public function getInvoice(): Invoice
    {
        $customer = new Customer();
        $customer->setName('Name');
        $customer->setBillingEmail('max.mustermann@example.org');
        $customer->setBrandSettings(new BrandSettings());
        $customer->getBrandSettings()->setBrandName('Dummy Brand');
        $customer->getBrandSettings()->setAddress(new Address());

        $invoice = new Invoice();
        $invoice->setCreatedAt(new \DateTime('now'));
        $invoice->setCustomer($customer);

        $lineOne = new InvoiceLine();
        $lineOne->setInvoice($invoice);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setTaxTotal(2000);
        $lineOne->setDescription('Example Line One');
        $lineOne->setTaxCountry('DE');

        $lineTwo = new InvoiceLine();
        $lineTwo->setInvoice($invoice);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setTaxTotal(4000);
        $lineTwo->setDescription('Example Line Two');
        $lineTwo->setTaxCountry('DE');

        $invoice->setLines(new ArrayCollection([$lineOne, $lineTwo]));
        $invoice->setTotal(30000);
        $invoice->setSubTotal(24000);
        $invoice->setTaxTotal(6000);
        $invoice->setCurrency('EUR');
        $invoice->setValid(true);

        $payeeAddress = new Address();
        $payeeAddress->setCompanyName('Company One');
        $payeeAddress->setStreetLineOne('One Example Strasse');
        $payeeAddress->setRegion('Berlin');
        $payeeAddress->setCity('Berlin');
        $payeeAddress->setCountry('Germany');
        $payeeAddress->setPostcode('10366');

        $invoice->setPayeeAddress($payeeAddress);

        $billerAddress = new Address();
        $billerAddress->setCompanyName('Company One');
        $billerAddress->setStreetLineOne('Two Example Straße');
        $billerAddress->setRegion('Berlin');
        $billerAddress->setCity('Berlin');
        $billerAddress->setCountry('Germany');
        $billerAddress->setPostcode('10366');

        $invoice->setBillerAddress($billerAddress);

        $invoice->setInvoiceNumber('SKDLSk');

        return $invoice;
    }
}
