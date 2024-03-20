<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Customers;

use App\Entity\Customer;
use App\Enum\CustomerStatus;
use App\Enum\CustomerType;
use App\Repository\Orm\BrandSettingsRepository;
use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Common\Address;

class MainContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private BrandSettingsRepository $brandSettingRepository,
    ) {
    }

    /**
     * @When I request the limits for customer :arg1
     */
    public function iRequestTheLimitsForCustomer($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/limits');
    }

    /**
     * @Then I should see that :arg1 is limited to :arg2
     */
    public function iShouldSeeThatIsLimitedTo($limitName, $limit)
    {
        $data = $this->getJsonContent();

        if (!isset($data['limits'][$limitName])) {
            throw new \Exception('Limit not set');
        }

        if ($data['limits'][$limitName] != $limit) {
            throw new \Exception('Limit is not the same');
        }
    }

    /**
     * @Then I should see the limit info that :arg1 is limited to :arg2
     */
    public function iShouldSeeTheLimitInfoThatIsLimitedTo($limitName, $limit)
    {
        $data = $this->getJsonContent();

        if (!isset($data['limits']['limits'][$limitName])) {
            throw new \Exception('Limit not set');
        }

        if ($data['limits']['limits'][$limitName] != $limit) {
            throw new \Exception('Limit is not the same');
        }
    }

    /**
     * @When I create a customer with the following info
     */
    public function iCreateACustomerWithTheFollowingInfo(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
            'email' => $data['Email'],
        ];

        if (isset($data['Country'])) {
            $payload['address'] = [
                'country' => $data['Country'],
            ];
        }

        if (isset($data['Type'])) {
            $payload['type'] = strtolower($data['Type']);
        }

        if (isset($data['External Reference'])) {
            $payload['external_reference'] = $data['External Reference'];
        }

        if (isset($data['Reference'])) {
            $payload['reference'] = $data['Reference'];
        }

        if (isset($data['Billing Type'])) {
            $payload['billing_type'] = $data['Billing Type'];
        }

        if (isset($data['Tax Number'])) {
            $payload['tax_number'] = $data['Tax Number'];
        }

        $this->sendJsonRequest('POST', '/api/v1/customer', $payload);
    }

    /**
     * @Then the customer :arg1 should have the billing type :arg2
     */
    public function theCustomerShouldHaveTheBillingType($email, $billingType)
    {
        $customer = $this->getCustomerByEmail($email);

        if ($customer->getBillingType() !== $billingType) {
            throw new \Exception(sprintf("Found '%s' instead of '%s'", $customer->getBillingType(), $billingType));
        }
    }

    /**
     * @When I update the customer info via the API for :arg1 with:
     */
    public function iUpdateTheCustomerInfoViaTheApiForWith($email, TableNode $table)
    {
        $customer = $this->getCustomerByEmail($email);

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

        $this->sendJsonRequest('PUT', '/api/v1/customer/'.$customer->getId(), $payload);
    }

    /**
     * @Then there should be a customer for :arg1
     */
    public function thereShouldBeACustomerFor($email)
    {
        try {
            $this->getCustomerByEmail($email);
        } catch (\Throwable $e) {
            var_dump($this->getJsonContent());
            throw $e;
        }
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
     * @Then the customer :arg1 should have the post code :arg2
     */
    public function theCustomerShouldHaveThePostCode($email, $arg2)
    {
        $customer = $this->getCustomerByEmail($email);

        if ($customer->getBillingAddress()->getPostcode() !== $arg2) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $arg2, $customer->getBillingAddress()->getPostcode()));
        }
    }

    /**
     * @Then the customer :arg1 should have the brand :arg2
     */
    public function theCustomerShouldHaveTheBrand($email, $brand)
    {
        $customer = $this->getCustomerByEmail($email);
        if ($customer->getBrand() !== $brand) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $brand, $customer->getBrand()));
        }
    }

    /**
     * @Then the customer :arg1 should have the locale :arg2
     */
    public function theCustomerShouldHaveTheLocale($email, $locale)
    {
        $customer = $this->getCustomerByEmail($email);
        if ($customer->getLocale() !== $locale) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $locale, $customer->getLocale()));
        }
    }

    /**
     * @Then the customer :arg1 should have the tax number :arg2
     */
    public function theCustomerShouldHaveTheTaxNumber($email, $taxNumber)
    {
        $customer = $this->getCustomerByEmail($email);
        if ($customer->getTaxNumber() !== $taxNumber) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $taxNumber, $customer->getTaxNumber()));
        }
    }

    /**
     * @Then the customer :arg1 should be a business customer
     */
    public function theCustomerShouldBeABusinessCustomer($email)
    {
        $customer = $this->getCustomerByEmail($email);
        if (CustomerType::BUSINESS !== $customer->getType()) {
            throw new \Exception('Not a business customer');
        }
    }

    /**
     * @Then the customer :arg1 should be a individual customer
     */
    public function theCustomerShouldBeAIndividualCustomer($email)
    {
        $customer = $this->getCustomerByEmail($email);
        if (CustomerType::INDIVIDUAL !== $customer->getType()) {
            throw new \Exception('Not a INDIVIDUAL customer');
        }
    }

    /**
     * @When I disable the customer info via the site for :arg1
     */
    public function iDisableTheCustomerInfoViaTheSiteFor($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/disable');
    }

    /**
     * @Then the customer :arg1 is disabled
     */
    public function theCustomerIsDisabled($email)
    {
        $customer = $this->getCustomerByEmail($email);
        if (!$customer->isDisabled()) {
            throw new \Exception('Not disabled');
        }
    }

    /**
     * @When customer :arg1 is disabled
     */
    public function customerIsDisabled($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $customer->setStatus(CustomerStatus::DISABLED);
        $this->customerRepository->getEntityManager()->persist($customer);
        $this->customerRepository->getEntityManager()->flush();
    }

    /**
     * @Then I should see an empty limits response
     */
    public function iShouldSeeAnEmptyLimitsResponse()
    {
        $data = $this->getJsonContent();

        if (!isset($data['limits']['limits'])) {
            throw new \Exception('Limit not set');
        }

        if (!empty($data['limits']['limits'])) {
            throw new \Exception('Limit is not empty');
        }
    }

    /**
     * @Then I should see an empty limits API response
     */
    public function iShouldSeeAnEmptyLimitsApiResponse()
    {
        $data = $this->getJsonContent();

        if (!isset($data['limits'])) {
            throw new \Exception('Limit not set');
        }

        if (!empty($data['limits'])) {
            throw new \Exception('Limit is not empty');
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
            $customer->setBillingType(strtolower($row['Billing Type'] ?? Customer::DEFAULT_BILLING_TYPE));

            $brand = $row['Brand'] ?? Customer::DEFAULT_BRAND;
            $brandSettings = $this->brandSettingRepository->findOneBy(['code' => $brand]);
            $customer->setBrandSettings($brandSettings);
            $customer->setBrand($brand);
            $customer->setCreatedAt(new \DateTime('now'));

            if (isset($row['Tax Number']) && !empty($row['Tax Number'])) {
                $customer->setTaxNumber($row['Tax Number']);
            } else {
                $customer->setTaxNumber(null);
            }

            $type = match (strtolower($row['Type'] ?? 'business')) {
                'business' => CustomerType::BUSINESS,
                default => CustomerType::INDIVIDUAL,
            };
            $customer->setType($type);

            if (isset($row['Digital Tax Rate']) && !empty($row['Digital Tax Rate'])) {
                $customer->setDigitalTaxRate(floatval($row['Digital Tax Rate']));
            }
            if (isset($row['Standard Tax Rate']) && !empty($row['Standard Tax Rate'])) {
                $customer->setStandardTaxRate(floatval($row['Standard Tax Rate']));
            }

            $this->customerRepository->getEntityManager()->persist($customer);
            $this->customerRepository->getEntityManager()->flush();

            if (!isset($row['Add Card']) || 'true' == strtolower($row['Add Card'])) {
                $paymentDetails = new PaymentCard();
                $paymentDetails->setCustomer($customer);
                $paymentDetails->setProvider('test_dummy');
                $paymentDetails->setName('Test');
                $paymentDetails->setCreatedAt(new \DateTime());
                $paymentDetails->setStoredCustomerReference($externalCustomerReference);
                $paymentDetails->setLastFour('4242');
                $paymentDetails->setExpiryMonth('02');
                $paymentDetails->setExpiryYear('32');
                $paymentDetails->setBrand('brand');
                $paymentDetails->setDefaultPaymentOption(true);
                $paymentDetails->setDeleted(false);
                $paymentDetails->setStoredPaymentReference($row['Payment Reference'] ?? bin2hex(random_bytes(32)));
                $this->customerRepository->getEntityManager()->persist($paymentDetails);
                $this->customerRepository->getEntityManager()->flush();
            }
        }

        $this->customerRepository->getEntityManager()->flush();
    }

    /**
     * @When I use the API to list customers
     */
    public function iUseTheApiToListCustomers()
    {
        $this->sendJsonRequest('GET', '/api/v1/customer');
    }

    /**
     * @When I use the API to list customers with parameter :arg1 with value :arg2
     */
    public function iUseTheApiToListCustomersWithParameterWithValue($filter, $value)
    {
        $this->sendJsonRequest('GET', sprintf('/api/v1/customer?%s=%s', $filter, $value));
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

        $this->sendJsonRequest('GET', sprintf('/api/v1/customer?last_key=%s', $data['last_key']));
    }

    /**
     * @When I view the customer info via the API for :arg1
     */
    public function iViewTheCustomerInfoViaTheApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('GET', sprintf('/api/v1/customer/%s', $customer->getId()));
    }

    /**
     * @When I disable the customer info via the API for :arg1
     */
    public function iDisableTheCustomerInfoViaTheApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('POST', sprintf('/api/v1/customer/%s/disable', $customer->getId()));
    }

    /**
     * @When I enable the customer info via the API for :arg1
     */
    public function iEnableTheCustomerInfoViaTheApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('POST', sprintf('/api/v1/customer/%s/enable', $customer->getId()));
    }

    /**
     * @Then the customer :arg1 is enabled
     */
    public function theCustomerIsEnabled($email)
    {
        $customer = $this->getCustomerByEmail($email);
        if ($customer->isDisabled()) {
            throw new \Exception('Is disabled');
        }
    }
}
