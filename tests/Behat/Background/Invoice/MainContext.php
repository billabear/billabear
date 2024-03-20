<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Background\Invoice;

use App\Background\Invoice\DisableOverdueInvoices;
use App\Background\Invoice\GenerateNewInvoices;
use App\Background\Invoice\UnpaidInvoices;
use App\Entity\Invoice;
use App\Entity\Processes\InvoiceProcess;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\InvoiceProcessRepository;
use App\Repository\Orm\InvoiceRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use Behat\Behat\Context\Context;

class MainContext implements Context
{
    use CustomerTrait;

    public function __construct(
        private GenerateNewInvoices $generateNewInvoices,
        private UnpaidInvoices $unpaidInvoices,
        private CustomerRepository $customerRepository,
        private InvoiceRepository $invoiceRepository,
        private InvoiceProcessRepository $invoiceProcessRepository,
        private DisableOverdueInvoices $disableOverdueInvoices,
    ) {
    }

    /**
     * @When the background task to reinvoice active subscriptions
     */
    public function theBackgroundTaskToReinvoiceActiveSubscriptions()
    {
        $this->generateNewInvoices->execute();
    }

    /**
     * @Then the latest invoice for :arg1 will have amount due as :arg2
     */
    public function theLatestInvoiceForWillHaveAmountDueAs($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);

        if ($invoice->getAmountDue() != $amount) {
            throw new \Exception('Different amount due - '.$invoice->getAmountDue());
        }
    }

    /**
     * @Then the latest invoice for :arg1 will be due in :arg2
     */
    public function theLatestInvoiceForWillBeDueIn($customerEmail, $days)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);

        $when = new \DateTime('+'.$days);
        if ($invoice->getDueAt()->format('Y-m-d') != $when->format('Y-m-d')) {
            throw new \Exception("Incorrect dates got '".$invoice->getDueAt()->format('Y-m-d')."' instead of '".$when->format('Y-m-d')."'");
        }
    }

    /**
     * @Then the latest invoice for :arg1 will have the invoice number :arg2
     */
    public function theLatestInvoiceForWillHaveTheInvoiceNumber($customerEmail, $invoiceNumber)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);

        if ($invoice->getInvoiceNumber() != $invoiceNumber) {
            throw new \Exception('Different invoice number - '.$invoice->getInvoiceNumber());
        }
    }

    /**
     * @Then the latest invoice for :arg1 will have tax amount due
     */
    public function theLatestInvoiceForWillHaveTaxAmountDue($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);

        if (!$invoice->getTaxTotal()) {
            throw new \Exception('Different amount due - '.$invoice->getTaxTotal());
        }
    }

    /**
     * @Then the latest invoice for :arg1 will not have tax amount due
     */
    public function theLatestInvoiceForWillNotHaveTaxAmountDue($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);

        if ($invoice->getTaxTotal()) {
            throw new \Exception('Different amount due - '.$invoice->getTaxTotal());
        }
    }

    /**
     * @When the background task to send reminders for unpaid invoices
     */
    public function theBackgroundTaskToSendRemindersForUnpaidInvoices()
    {
        $this->unpaidInvoices->execute();
    }

    /**
     * @When I run the background task to cancel overdue customers
     */
    public function iRunTheBackgroundTaskToCancelOverdueCustomers()
    {
        $this->disableOverdueInvoices->execute();
    }

    /**
     * @Then the workflow for the invoice for :arg1 will be at :arg2
     */
    public function theWorkflowForTheInvoiceForWillBeAt($customerEmail, $state)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);
        /** @var InvoiceProcess $invoiceProcess */
        $invoiceProcess = $this->invoiceProcessRepository->findOneBy(['invoice' => $invoice]);

        if ($invoiceProcess->getState() !== $state) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $state, $invoiceProcess->getState()));
        }
    }
}
