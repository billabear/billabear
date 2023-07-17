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

namespace App\Tests\Behat\Tax;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Enum\TaxType;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\InvoiceRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use Behat\Behat\Context\Context;

class InvoiceContext implements Context
{
    use CustomerTrait;

    public function __construct(
        private CustomerRepository $customerRepository,
        private InvoiceRepository $invoiceRepository,
    ) {
    }

    /**
     * @Then there the latest invoice for :arg1 will have tax rate for UK
     */
    public function thereTheLatestInvoiceForWillHaveTaxRateForUk($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if (20 == $line->getVatPercentage()) {
                return;
            } else {
                $rate = $line->getVatPercentage();
            }
        }

        throw new \Exception('Got rate - '.$rate);
    }

    /**
     * @When there the latest invoice for :arg1 will have tax rate of :arg2
     */
    public function thereTheLatestInvoiceForWillHaveTaxRateOf($customerEmail, $expectedRate)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->getVatPercentage() == $expectedRate) {
                return;
            } else {
                $rate = $line->getVatPercentage();
            }
        }

        throw new \Exception('Got rate - '.$rate);
    }

    /**
     * @Then there the latest invoice for :arg1 will have tax type for digital goods
     */
    public function thereTheLatestInvoiceForWillHaveTaxTypeForDigitalGoods($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $taxType = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if (TaxType::DIGITAL_GOODS == $line->getTaxType()) {
                return;
            } else {
                $taxType = $line->getTaxType()->value;
            }
        }

        throw new \Exception('Got taxType - '.$taxType);
    }

    /**
     * @Then there the latest invoice for :arg1 will have tax type for physical goods
     */
    public function thereTheLatestInvoiceForWillHaveTaxTypeForPhysicalGoods($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $taxType = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if (TaxType::PHYSICAL == $line->getTaxType()) {
                return;
            } else {
                $taxType = $line->getTaxType()->value;
            }
        }

        throw new \Exception('Got taxType - '.$taxType);
    }
}
