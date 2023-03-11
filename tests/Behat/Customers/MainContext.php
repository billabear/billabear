<?php

namespace App\Tests\Behat\Customers;

use App\Entity\Customer;
use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class MainContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @When I create a customer with the following info
     */
    public function iCreateACustomerWithTheFollowingInfo(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
            'email' => $data['Email'],
            'country' => $data['Country'],
        ];

        if (isset($data['External Reference'])) {
            $payload['external_reference'] = $data['External Reference'];
        }

        if (isset($data['Reference'])) {
            $payload['reference'] = $data['Reference'];
        }

        $this->sendJsonRequest('PUT', '/api/1.0/customer', $payload);
    }

    /**
     * @Then there should be a customer for :arg1
     */
    public function thereShouldBeACustomerFor($email)
    {
        $this->getCustomerByEmail($email);
    }

    /**
     * @Then the customer :arg1 should have the external reference :arg2
     */
    public function theCustomerShouldHaveTheExternalReference($email, $arg2)
    {
        $customer = $this->getCustomerByEmail($email);

        if ($customer->getExternalCustomerReference() !== $arg2) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $arg2, $customer->getExternalCustomerReference()));
        }
    }

    /**
     * @Then the customer :arg1 should have the reference :arg2
     */
    public function theCustomerShouldHaveTheReference($email, $arg2)
    {
        $customer = $this->getCustomerByEmail($email);

        if ($customer->getReference() !== $arg2) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $arg2, $customer->getReference()));
        }
    }

    /**
     * @param $email
     * @return void
     * @throws \Exception
     */
    public function getCustomerByEmail($email): Customer
    {
        $customer = $this->customerRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            throw new \Exception(sprintf("No customer for '%s'", $email));
        }

        $this->customerRepository->getEntityManager()->refresh($customer);

        return $customer;
    }

}
