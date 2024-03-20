<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
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
            if (20 == $line->getTaxPercentage()) {
                return;
            } else {
                $rate = $line->getTaxPercentage();
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
            if ($line->getTaxPercentage() == $expectedRate) {
                return;
            } else {
                $rate = $line->getTaxPercentage();
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
            if ('Digital Goods' === $line->getTaxType()->getName()) {
                return;
            } else {
                $taxType = $line->getTaxType()->getName();
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
            if ('Physical' === $line->getTaxType()->getName()) {
                return;
            } else {
                $taxType = $line->getTaxType()->getName();
            }
        }

        throw new \Exception('Got taxType - '.$taxType);
    }

    /**
     * @When there the latest invoice for :arg1 will have tax country of :arg2
     */
    public function thereTheLatestInvoiceForWillHaveTaxCountryOf($customerEmail, $expectedCountry)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->getTaxCountry() == $expectedCountry) {
                return;
            } else {
                $rate = $line->getTaxCountry();
            }
        }

        throw new \Exception('Got country - '.$rate);
    }

    /**
     * @When there the latest invoice for :arg1 will have a reverse charge
     */
    public function thereTheLatestInvoiceForWillHaveAReverseCharge($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->isReverseCharge()) {
                return;
            }
        }

        throw new \Exception('No reverse charge items found');
    }

    /**
     * @When there the latest invoice for :arg1 will not have a zero tax rate
     */
    public function thereTheLatestInvoiceForWillNotHaveAZeroTaxRate($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if (0.0 === $line->getTaxPercentage()) {
                throw new \Exception('Found a zero rate tax item');
            }
        }
    }

    /**
     * @When there the latest invoice for :arg1 will not have a reverse charge
     */
    public function thereTheLatestInvoiceForWillNotHaveAReverseCharge($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->isReverseCharge()) {
                throw new \Exception('Found a reverse charge item');
            }
        }
    }

    /**
     * @When there the latest invoice for :arg1 will have a zero tax rate
     */
    public function thereTheLatestInvoiceForWillHaveAZeroTaxRate($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if (0.0 === $line->getTaxPercentage()) {
                return;
            } else {
                $rate = $line->getTaxPercentage();
            }
        }

        throw new \Exception('Got rate - '.$rate);
    }
}
