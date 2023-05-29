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

namespace App\Tests\Behat\Invoices;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\InvoiceRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class AppContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private InvoiceRepository $invoiceRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @Given the following invoices exist:
     */
    public function theFollowingInvoicesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $customer = $this->getCustomerByEmail($row['Customer']);

            $invoice = new Invoice();

            $line = new InvoiceLine();
            $line->setInvoice($invoice);
            $line->setTotal(10000);
            $line->setVatPercentage(20.0);
            $line->setSubTotal(8000);
            $line->setVatTotal(2000);
            $line->setDescription('A test line');
            $line->setCurrency('USD');
            $lines = [$line];

            $invoice->setCustomer($customer);
            $invoice->setInvoiceNumber(bin2hex(random_bytes(16)));
            $invoice->setCreatedAt(new \DateTime('now'));
            $invoice->setUpdatedAt(new \DateTime('now'));
            $invoice->setCurrency('USD');
            $invoice->setPaid(false);
            $invoice->setValid(true);
            $invoice->setLines($lines);
            $invoice->setTotal(10000);
            $invoice->setSubTotal(8000);
            $invoice->setVatTotal(2000);
            $invoice->setAmountDue(10000);
            $invoice->setBillerAddress($customer->getBillingAddress());
            $invoice->setPayeeAddress($customer->getBillingAddress());

            $this->invoiceRepository->getEntityManager()->persist($invoice);
        }

        $this->invoiceRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the invoice list
     */
    public function iViewTheInvoiceList()
    {
        $this->sendJsonRequest('GET', '/app/invoices');
    }

    /**
     * @Then I will see an invoice for :arg1
     */
    public function iWillSeeAnInvoiceFor($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $invoice) {
            if ($invoice['customer']['email'] === $arg1) {
                return;
            }
        }

        throw new \Exception('No invoice found');
    }

    /**
     * @Then there the latest invoice for :arg1 will not be marked as paid
     */
    public function thereTheLatestInvoiceForWillNotBeMarkedAsPaid($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        if (!$invoice instanceof Invoice) {
            throw new \Exception('No invoice found');
        }

        if ($invoice->isPaid()) {
            throw new \Exception('Invoice is marked as paid');
        }
    }
}
