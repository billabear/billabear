<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\PaymentDetails;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use PaymentDetailsTrait;

    public function __construct(
        private Session $session,
        private PaymentCardServiceRepository $paymentDetailsRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * @When the following customers have cards that will expire in 30 days:
     */
    public function theFollowingCustomersHaveCardsThatWillExpireIn30days(TableNode $table)
    {
        $rows = $table->getColumnsHash();
        $now = new \DateTime('+30 days');
        foreach ($rows as $row) {
            $customer = $this->getCustomerByEmail($row['Customer Email']);
            $paymentDetails = new PaymentCard();
            $paymentDetails->setName($row['Name'] ?? 'One');
            $paymentDetails->setBrand($row['Brand'] ?? 'dummy');
            $paymentDetails->setLastFour($row['Last Four']);
            $paymentDetails->setExpiryMonth((int) $now->format('m'));
            $paymentDetails->setExpiryYear((int) $now->format('Y'));
            $paymentDetails->setStoredPaymentReference(bin2hex(random_bytes(32)));
            $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
            $paymentDetails->setCustomer($customer);
            $paymentDetails->setCreatedAt(new \DateTime());
            $paymentDetails->setDefaultPaymentOption(true);

            $this->paymentDetailsRepository->getEntityManager()->persist($paymentDetails);
        }

        $this->paymentDetailsRepository->getEntityManager()->flush();
    }

    /**
     * @When the following customers have cards that will expire this month:
     */
    public function theFollowingCustomersHaveCardsThatWillExpireThisMonth(TableNode $table)
    {
        $rows = $table->getColumnsHash();
        $now = new \DateTime('now');
        foreach ($rows as $row) {
            $customer = $this->getCustomerByEmail($row['Customer Email']);
            $paymentDetails = new PaymentCard();
            $paymentDetails->setName($row['Name'] ?? 'One');
            $paymentDetails->setBrand($row['Brand'] ?? 'dummy');
            $paymentDetails->setLastFour($row['Last Four']);
            $paymentDetails->setExpiryMonth((int) $now->format('m'));
            $paymentDetails->setExpiryYear((int) $now->format('Y'));
            $paymentDetails->setStoredPaymentReference(bin2hex(random_bytes(32)));
            $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
            $paymentDetails->setCustomer($customer);
            $paymentDetails->setCreatedAt(new \DateTime());
            $paymentDetails->setDefaultPaymentOption(true);

            $this->paymentDetailsRepository->getEntityManager()->persist($paymentDetails);
        }

        $this->paymentDetailsRepository->getEntityManager()->flush();
    }

    /**
     * @Given the following customers have cards that will expired last month:
     */
    public function theFollowingCustomersHaveCardsThatWillExpiredLastMonth(TableNode $table)
    {
        $rows = $table->getColumnsHash();
        $lastMonth = new \DateTime('-1 month');
        foreach ($rows as $row) {
            $customer = $this->getCustomerByEmail($row['Customer Email']);
            $paymentDetails = new PaymentCard();
            $paymentDetails->setName($row['Name'] ?? 'One');
            $paymentDetails->setBrand($row['Brand'] ?? 'dummy');
            $paymentDetails->setLastFour($row['Last Four']);
            $paymentDetails->setExpiryMonth((int) $lastMonth->format('m'));
            $paymentDetails->setExpiryYear((int) $lastMonth->format('Y'));
            $paymentDetails->setStoredPaymentReference(bin2hex(random_bytes(32)));
            $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
            $paymentDetails->setCustomer($customer);
            $paymentDetails->setCreatedAt(new \DateTime());
            $paymentDetails->setDefaultPaymentOption(true);

            $this->paymentDetailsRepository->getEntityManager()->persist($paymentDetails);
        }

        $this->paymentDetailsRepository->getEntityManager()->flush();
    }

    /**
     * @When the following payment details:
     */
    public function theFollowingPaymentDetails(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $customer = $this->getCustomerByEmail($row['Customer Email']);
            $paymentDetails = new PaymentCard();
            $paymentDetails->setName($row['Name'] ?? 'One');
            $paymentDetails->setBrand($row['Brand'] ?? 'dummy');
            $paymentDetails->setLastFour($row['Last Four']);
            $paymentDetails->setExpiryMonth($row['Expiry Month']);
            $paymentDetails->setExpiryYear($row['Expiry Year']);
            $paymentDetails->setStoredPaymentReference(bin2hex(random_bytes(32)));
            $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
            $paymentDetails->setCustomer($customer);
            $paymentDetails->setCreatedAt(new \DateTime());
            $paymentDetails->setDefaultPaymentOption(($row['Default'] ?? 'false') !== 'false');

            $this->paymentDetailsRepository->getEntityManager()->persist($paymentDetails);
        }

        $this->paymentDetailsRepository->getEntityManager()->flush();
    }

    /**
     * @Then I will see :arg1 payment details
     */
    public function iWillSeePaymentDetails($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['payment_details'])) {
            throw new \Exception('No payment details found');
        }

        if (count($data['payment_details']) != $arg1) {
            throw new \Exception('Wrong count');
        }
    }

    /**
     * @Then I will see the payment details with the last four :arg1
     */
    public function iWillSeeThePaymentDetailsWithTheLastFour($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['payment_details'])) {
            throw new \Exception('No payment details found');
        }

        foreach ($data['payment_details'] as $paymentDetails) {
            if ($paymentDetails['last_four'] == $arg1) {
                return;
            }
        }

        throw new \Exception("Can't find payment details");
    }

    /**
     * @When I view the payment details info for the customer via the site for :arg1
     */
    public function iViewThePaymentDetailsInfoForTheCustomerViaTheSiteFor($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('GET', '/app/customer/'.$customer->getId().'/payment-card');
    }

    /**
     * @When I make the payment details :arg1 for :arg2 default via APP
     */
    public function iMakeThePaymentDetailsForDefault($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/payment-card/'.$paymentDetails->getId().'/default');
    }

    /**
     * @When I delete the payment details :arg1 for :arg2 via APP
     */
    public function iDeleteThePaymentDetailsFor($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('DELETE', '/app/customer/'.$customer->getId().'/payment-card/'.$paymentDetails->getId());
    }
}
