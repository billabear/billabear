<?php

namespace App\Tests\Behat\Customers;

use App\Entity\Customer;
use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Common\Address;

class MainContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

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
            'address' => [
                'country' => $data['Country'],
            ],
        ];

        if (isset($data['External Reference'])) {
            $payload['external_reference'] = $data['External Reference'];
        }

        if (isset($data['Reference'])) {
            $payload['reference'] = $data['Reference'];
        }

        $this->sendJsonRequest('PUT', '/api/v1.0/customer', $payload);
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
     * @Given the follow customers exist:
     */
    public function theFollowCustomersExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $externalCustomerReference = $row['External Reference'] ?? bin2hex(random_bytes(32));
            $reference = $row['Reference'] ?? null;

            $billingAddress = new Address();
            $billingAddress->setCountry($row['Country']);

            $customer = new Customer();
            $customer->setBillingEmail($row['Email']);
            $customer->setBillingAddress($billingAddress);
            $customer->setExternalCustomerReference($externalCustomerReference);
            $customer->setReference($reference);

            $this->customerRepository->getEntityManager()->persist($customer);
        }

        $this->customerRepository->getEntityManager()->flush();
    }

    /**
     * @When I use the API to list customers
     */
    public function iUseTheApiToListCustomers()
    {
        $this->sendJsonRequest('GET', '/api/v1.0/customer');
    }

    /**
     * @When I use the API to list customers with parameter :arg1 with value :arg2
     */
    public function iUseTheApiToListCustomersWithParameterWithValue($filter, $value)
    {
        $this->sendJsonRequest('GET', sprintf('/api/v1.0/customer?%s=%s', $filter, $value));
    }

    /**
     * @Then I should not see in the API response the customer :arg1
     */
    public function iShouldNotSeeInTheApiResponseTheCustomer($email)
    {
        $data = $this->getJsonContent();

        if (!isset($data['data'])) {
            throw new \Exception('No data found');
        }

        foreach ($data['data'] as $customer) {
            if ($customer['email'] === $email) {
                throw new \Exception('Found customer');
            }
        }
    }

    /**
     * @Then I should see in the API response the customer :arg1
     */
    public function iShouldSeeInTheApiResponseTheCustomer($email)
    {
        $data = $this->getJsonContent();

        if (!isset($data['data'])) {
            throw new \Exception('No data found');
        }

        foreach ($data['data'] as $customer) {
            if ($customer['email'] === $email) {
                return;
            }
        }

        throw new \Exception("Can't find customer");
    }

    /**
     * @When I use the API to list customers with the last_key from the last response
     */
    public function iUseTheApiToListCustomersWithTheLastKeyFromTheLastResponse()
    {
        $data = $this->getJsonContent();

        $this->sendJsonRequest('GET', sprintf('/api/v1.0/customer?last_key=%s', $data['last_key']));
    }

    /**
     * @When I view the customer info via the API for :arg1
     */
    public function iViewTheCustomerInfoViaTheApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('GET', sprintf('/api/v1.0/customer/%s', $customer->getId()));
    }
}
