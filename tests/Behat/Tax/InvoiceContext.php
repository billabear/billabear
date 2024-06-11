<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\InvoiceRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;

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
     * @Then there the latest invoice for :arg1 will have tax state of :arg2
     */
    public function thereTheLatestInvoiceForWillHaveTaxStateOf($customerEmail, $expectedState)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->getTaxState() == $expectedState) {
                return;
            }
        }

        throw new \Exception('Did not get state');
    }

    /**
     * @Then there the latest invoice for :arg1 will have tax rate of :arg2
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
     * @Then there will be an invoice for a partial amount of :arg3 for :arg1 for :arg2
     */
    public function thereWillBeAnInvoiceForAPartialAmountOfForFor($amount, $planName, $customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $amount = (int) $amount;

        if (27000 == $invoice->getAmountDue()) {
            throw new \Exception('Has crisp amount');
        }

        if ($invoice->getAmountDue() < $amount) {
            return;
        }

        throw new \Exception(sprintf('Expected %d but got %d', $amount, $invoice->getAmountDue()));
    }

    /**
     * @Then there the latest invoice for :arg1 will not have tax country of :arg2
     */
    public function thereTheLatestInvoiceForWillNotHaveTaxCountryOf($customerEmail, $expectedCountry)
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
                throw new \Exception('Found country');
            }
        }
    }

    /**
     * @Then there the latest invoice for :arg1 will not have tax state of :arg2
     */
    public function thereTheLatestInvoiceForWillNotHaveTaxStateOf($customerEmail, $expectedState)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $rate = null;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            if ($line->getTaxState() == $expectedState) {
                throw new \Exception('Found state');
            }
        }
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
