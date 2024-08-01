<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Invoices;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\DataMappers\PaymentAttemptDataMapper;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceDelivery;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Enum\InvoiceDeliveryType;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\InvoiceDeliveryRepository;
use BillaBear\Repository\Orm\InvoiceRepository;
use BillaBear\Repository\Orm\PaymentAttemptRepository;
use BillaBear\Repository\Orm\PaymentFailureProcessRepository;
use BillaBear\Repository\Orm\SubscriptionPlanRepository;
use BillaBear\Repository\Orm\TaxTypeRepository;
use BillaBear\Repository\SubscriptionRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use BillaBear\Tests\Behat\Subscriptions\SubscriptionTrait;
use Obol\Model\Enum\ChargeFailureReasons;

class AppContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;
    use SubscriptionTrait;

    public function __construct(
        private Session $session,
        private InvoiceRepository $invoiceRepository,
        private CustomerRepository $customerRepository,
        private PaymentAttemptDataMapper $paymentAttemptFactory,
        private PaymentAttemptRepository $paymentAttemptRepository,
        private PaymentFailureProcessRepository $paymentFailureProcessRepository,
        private SubscriptionRepository $subscriptionRepository,
        private SubscriptionPlanRepository $planRepository,
        private TaxTypeRepository $taxTypeRepository,
        private InvoiceDeliveryRepository $invoiceDeliveryRepository,
    ) {
    }

    /**
     * @Given the following invoices exist:
     */
    public function theFollowingInvoicesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $this->createInvoice($row);
        }

        $this->invoiceRepository->getEntityManager()->flush();
    }

    /**
     * @Given the following invoices with a payment attempt exist:
     */
    public function theFollowingInvoicesWithAPaymentAttemptExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $invoice = $this->createInvoice($row);

            $paymentAttempt = $this->paymentAttemptFactory->createFromInvoice($invoice, ChargeFailureReasons::CONTACT_PROVIDER);
            $paymentAttempt->setSubscriptions($invoice->getCustomer()->getSubscriptions());
            $this->paymentAttemptRepository->getEntityManager()->persist($paymentAttempt);
            $this->paymentAttemptRepository->getEntityManager()->flush();

            $paymentFailureProcess = new PaymentFailureProcess();
            $paymentFailureProcess->setPaymentAttempt($paymentAttempt);
            $paymentFailureProcess->setCustomer($paymentAttempt->getCustomer());
            $paymentFailureProcess->setRetryCount(intval($row['Retry Count'] ?? 0));
            $paymentFailureProcess->setNextAttemptAt(new \DateTime($row['Next Attempt'] ?? '+2 days'));
            $paymentFailureProcess->setState('payment_retries');
            $paymentFailureProcess->setUpdatedAt(new \DateTime('now'));
            $paymentFailureProcess->setCreatedAt(new \DateTime('now'));
            $paymentFailureProcess->setResolved(false);

            $this->paymentAttemptRepository->getEntityManager()->persist($paymentFailureProcess);
            $this->paymentAttemptRepository->getEntityManager()->flush();
        }
    }

    /**
     * @Then the retry count for payment failure process for :arg1 will be :arg2
     */
    public function theRetryCountForPaymentFailureProcessForWillBe($customerEmail, $count)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $paymentFailureProcess = $this->paymentFailureProcessRepository->findOneBy(['customer' => $customer]);

        if (!$paymentFailureProcess instanceof PaymentFailureProcess) {
            throw new \Exception('No payment failure found');
        }
        $this->paymentAttemptRepository->getEntityManager()->refresh($paymentFailureProcess);

        if ($paymentFailureProcess->getRetryCount() !== intval($count)) {
            throw new \Exception('Found retry count '.$paymentFailureProcess->getRetryCount());
        }
    }

    /**
     * @When charge the invoice for :arg1
     */
    public function chargeTheInvoiceFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        $this->sendJsonRequest('POST', '/app/invoice/'.$invoice->getId().'/charge');
    }

    /**
     * @When charge the invoice for :arg1 via API
     */
    public function chargeTheInvoiceForViaApi($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        $this->sendJsonRequest('POST', '/api/v1/invoice/'.$invoice->getId().'/charge');
    }

    /**
     * @When I mark the invoice for :arg1 as paid
     */
    public function iMarkTheInvoiceForAsPaid($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);

        $this->sendJsonRequest('POST', '/app/invoice/'.$invoice->getId().'/paid');
    }

    /**
     * @When I view the invoice for :arg1
     */
    public function iViewTheInvoiceFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);
        $this->sendJsonRequest('GET', '/app/invoice/'.$invoice->getId().'/view');
    }

    /**
     * @Then I should see the invoice for :arg1
     */
    public function iShouldSeeTheInvoiceFor($arg1)
    {
        $invoice = $this->getJsonContent();

        if (!isset($invoice['invoice']['customer']['email']) || $invoice['invoice']['customer']['email'] != $arg1) {
            throw new \Exception("Can't see the correct invoice");
        }
    }

    /**
     * @Then then the invoice for :arg1 will be marked as paid
     */
    public function thenTheInvoiceForWillBeMarkedAsPaid($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);
        $this->invoiceRepository->getEntityManager()->refresh($invoice);

        if (!$invoice->isPaid()) {
            throw new \Exception('Invoice not paid');
        }
    }

    /**
     * @Then there will be an unpaid invoice for :arg1
     */
    public function thereWillBeAnUnpaidInvoiceFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);
        if (!$invoice) {
            var_dump($this->getJsonContent());
            throw new \Exception('No invoice found');
        }
        $this->invoiceRepository->getEntityManager()->refresh($invoice);

        if ($invoice->isPaid()) {
            throw new \Exception('Invoice not paid');
        }
    }

    /**
     * @Then then the invoice for :arg1 will not be marked as paid
     */
    public function thenTheInvoiceForWillNotBeMarkedAsPaid($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $invoice = $this->invoiceRepository->findOneBy(['customer' => $customer]);
        $this->invoiceRepository->getEntityManager()->refresh($invoice);

        if ($invoice->isPaid()) {
            throw new \Exception('Invoice paid');
        }
    }

    /**
     * @When I view the unpaid invoice list
     */
    public function iViewTheUnpaidInvoiceList()
    {
        $this->sendJsonRequest('GET', '/app/invoices/unpaid');
    }

    /**
     * @When I view the invoice list
     */
    public function iViewTheInvoiceList()
    {
        $this->sendJsonRequest('GET', '/app/invoices');
    }

    /**
     * @When I view the invoice list for :arg1
     */
    public function iViewTheInvoiceListFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/invoices');
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
     * @Then I will not see an invoice for :arg1
     */
    public function iWillNotSeeAnInvoiceFor($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $invoice) {
            if ($invoice['customer']['email'] === $arg1) {
                throw new \Exception('Invoice found');
            }
        }
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

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function createInvoice(mixed $row): Invoice
    {
        $customer = $this->getCustomerByEmail($row['Customer']);

        $invoice = new Invoice();

        $line = new InvoiceLine();
        $line->setInvoice($invoice);
        $line->setTotal(10000);
        $line->setTaxPercentage(20.0);
        $line->setSubTotal(8000);
        $line->setTaxTotal(2000);
        $line->setDescription('A test line');
        $line->setCurrency('USD');
        if (isset($row['Tax Type'])) {
            $taxType = $this->taxTypeRepository->findOneBy(['name' => $row['Tax Type']]);
            $line->setTaxType($taxType);
        }

        $lines = [$line];

        $invoice->setCustomer($customer);
        $invoice->setInvoiceNumber($row['Invoice Number'] ?? bin2hex(random_bytes(16)));
        $invoice->setCreatedAt(new \DateTime('now'));
        $invoice->setUpdatedAt(new \DateTime('now'));
        $invoice->setCurrency('USD');
        $invoice->setPaid('true' === strtolower($row['Paid'] ?? 'true'));
        $invoice->setValid(true);
        $invoice->setLines($lines);
        $invoice->setTotal(10000);
        $invoice->setSubTotal(8000);
        $invoice->setTaxTotal(2000);
        $invoice->setAmountDue(10000);
        $invoice->setBillerAddress($customer->getBillingAddress());
        $invoice->setPayeeAddress($customer->getBillingAddress());

        if (isset($row['Due Date'])) {
            $invoice->setDueAt(new \DateTime($row['Due Date']));
        }

        $this->invoiceRepository->getEntityManager()->persist($invoice);
        $this->invoiceRepository->getEntityManager()->flush();

        $invoiceProcess = new InvoiceProcess();
        if (isset($row['State'])) {
            $state = $row['State'];
        } else {
            $state = $invoice->isPaid() ? 'paid' : 'internal_notification_sent';
        }

        $invoiceProcess->setState($state);
        $invoiceProcess->setCustomer($invoice->getCustomer());
        $invoiceProcess->setInvoice($invoice);
        $invoiceProcess->setCreatedAt(new \DateTime('now'));
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));
        $invoiceProcess->setDueAt($invoice->getDueAt());

        $this->invoiceRepository->getEntityManager()->persist($invoiceProcess);
        $this->invoiceRepository->getEntityManager()->flush();

        return $invoice;
    }

    /**
     * @When I create a delivery method for :arg1 with the following settings:
     */
    public function iCreateADeliveryMethodForWithTheFollowingSettings($email, TableNode $table)
    {
        $customer = $this->getCustomerByEmail($email);

        $data = $table->getRowsHash();

        $payload = [
            'type' => strtolower($data['Type']),
        ];

        if (isset($data['SFTP Host'])) {
            $payload['sftp_host'] = $data['SFTP Host'];
        }

        if (isset($data['SFTP Port'])) {
            $payload['sftp_port'] = (int) $data['SFTP Port'];
        }

        if (isset($data['SFTP Dir'])) {
            $payload['sftp_dir'] = $data['SFTP Dir'];
        }

        if (isset($data['SFTP User'])) {
            $payload['sftp_user'] = $data['SFTP User'];
        }

        if (isset($data['SFTP Password'])) {
            $payload['sftp_password'] = $data['SFTP Password'];
        }

        if (isset($data['Webhook URL'])) {
            $payload['webhook_url'] = $data['Webhook URL'];
        }

        if (isset($data['Webhook Method'])) {
            $payload['webhook_method'] = $data['Webhook Method'];
        }

        $this->sendJsonRequest('POST', sprintf('/app/customer/%s/invoice-delivery', (string) $customer->getId()), $payload);
    }

    /**
     * @Then there should be an invoice delivery for :arg1 for type :arg2
     */
    public function thereShouldBeAnInvoiceDeliveryForForType($email, $type)
    {
        $customer = $this->getCustomerByEmail($email);
        $type = strtolower($type);
        $enumType = InvoiceDeliveryType::from($type);
        $invoiceDelivery = $this->invoiceDeliveryRepository->findOneBy(['type' => $enumType, 'customer' => $customer]);

        if (!$invoiceDelivery instanceof InvoiceDelivery) {
            var_dump($this->getJsonContent());
            throw new \Exception('No invoice delivery found');
        }
    }
}
