<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\PaymentDetails;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class ApiContext implements Context
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
     * @When I make the payment methods :arg1 for :arg2 default
     */
    public function iMakeThePaymentDetailsForDefault($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('POST', '/api/v1/customer/'.$customer->getId().'/payment-methods/'.$paymentDetails->getId().'/default');
    }

    /**
     * @When I delete the payment methods :arg1 for :arg2
     */
    public function iDeleteThePaymentDetailsFor($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('DELETE', '/api/v1/customer/'.$customer->getId().'/payment-methods/'.$paymentDetails->getId());
    }

    /**
     * @When I view the payment methods :arg1 for :arg2
     */
    public function iViewThePaymentMethodsFor($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('GET', '/api/v1/payment-methods/'.$paymentDetails->getId());
    }

    /**
     * @Then there should be the last should be :arg1
     */
    public function thereShouldBeTheLastShouldBe($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['last_four'] != $arg1) {
            throw new \Exception('Last four should be the same');
        }
    }

    /**
     * @When I fetch the payment details via API for customer :arg1
     */
    public function iFetchThePaymentDetailsViaApiForCustomer($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/payment-methods');
    }

    /**
     * @Then the response should have :arg1 items in the data array
     */
    public function theResponseShouldHaveItemsInTheDataArray($count)
    {
        $data = $this->getJsonContent();

        if (count($data['data']) != $count) {
            throw new \Exception(sprintf('Expected %d but got %d', $count, count($data['data'])));
        }
    }

    /**
     * @Then the response should contain the payment details for last four :arg1
     */
    public function theResponseShouldContainThePaymentDetailsForLastFour($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $paymentDetails) {
            if ($paymentDetails['last_four'] == $arg1) {
                return;
            }
        }

        throw new \Exception("Didn't find last four");
    }

    /**
     * @Then the response should not contain the payment details for last four :arg1
     */
    public function theResponseShouldNotContainThePaymentDetailsForLastFour($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $paymentDetails) {
            if ($paymentDetails['last_four'] == $arg1) {
                throw new \Exception('Found find last four');
            }
        }
    }
}
