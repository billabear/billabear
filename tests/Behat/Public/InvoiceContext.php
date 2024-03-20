<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Public;

use App\Entity\Invoice;
use App\Repository\Orm\InvoiceRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class InvoiceContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private InvoiceRepository $invoiceRepository)
    {
    }

    /**
     * @When I go to the invoice paylink for invoice :arg1
     */
    public function iGoToTheInvoicePaylinkForInvoice($arg1)
    {
        $invoice = $this->invoiceRepository->findOneBy(['invoiceNumber' => $arg1]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        $this->sendJsonRequest('GET', '/public/invoice/'.$invoice->getId().'/pay');
    }

    /**
     * @Then I should see the total amount
     */
    public function iShouldSeeTheTotalAmount()
    {
        $data = $this->getJsonContent();

        if (!isset($data['invoice']['amount'])) {
            throw new \Exception("Can't see amount");
        }
    }

    /**
     * @Then I should see the stripe frontend
     */
    public function iShouldSeeTheStripeFrontend()
    {
        $data = $this->getJsonContent();

        if (!isset($data['stripe']['token'])) {
            throw new \Exception("Can't see token");
        }
    }

    /**
     * @Then I should see that the invoice has been paid
     */
    public function iShouldSeeThatTheInvoiceHasBeenPaid()
    {
        $data = $this->getJsonContent();

        if (!isset($data['invoice']['paid']) || false == $data['invoice']['paid']) {
            throw new \Exception('Not marked as paid');
        }
    }
}
