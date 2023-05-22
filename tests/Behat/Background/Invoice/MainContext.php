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

namespace App\Tests\Behat\Background\Invoice;

use App\Background\Invoice\GenerateNewInvoices;
use App\Entity\Invoice;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\InvoiceRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use Behat\Behat\Context\Context;

class MainContext implements Context
{
    use CustomerTrait;

    public function __construct(
        private GenerateNewInvoices $generateNewInvoices,
        private CustomerRepository $customerRepository,
        private InvoiceRepository $invoiceRepository,
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
}
