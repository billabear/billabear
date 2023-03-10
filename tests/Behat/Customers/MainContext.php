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
    )
    {
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

        $this->sendJsonRequest('PUT', '/api/1.0/customer', $payload);
    }

    /**
     * @Then there should be a customer for :arg1
     */
    public function thereShouldBeACustomerFor($email)
    {
        $customer = $this->customerRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            print $this->session->getStatusCode();
            throw new \Exception(sprintf("No customer for '%s'", $email));
        }
    }



}