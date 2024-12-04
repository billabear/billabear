<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Usage;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\UsageLimit;
use BillaBear\Enum\WarningLevel;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\UsageLimitRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class CustomerContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private UsageLimitRepository $usageLimitRepository,
    ) {
    }

    /**
     * @When I add customer usage limit to :arg1:
     */
    public function iAddCustomerUsageLimitTo($customerEmail, TableNode $table): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $data = $table->getRowsHash();

        $warnLevel = $this->getLevel($data['Warning Type']);

        $payload = [
            'amount' => intval($data['Amount']),
            'warn_level' => $warnLevel->value,
        ];

        $this->sendJsonRequest('POST', sprintf('/app/customer/%s/usage-limit', $customer->getId()), $payload);
    }

    /**
     * @When I add customer usage limit via API to :arg1:
     */
    public function iAddCustomerUsageLimitViaApiTo($customerEmail, TableNode $table): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $data = $table->getRowsHash();

        $payload = [
            'amount' => intval($data['Amount']),
            'action' => strtoupper($data['Warning Type']),
        ];

        $this->sendJsonRequest('POST', sprintf('/api/v1/customer/%s/usage-limits', $customer->getId()), $payload);
    }

    /**
     * @Then there should be a limit to warn at :limit for :customerEmail
     */
    public function thereShouldBeALimitToWarnAtFor($limit, $customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $usageLimit = $this->usageLimitRepository->findOneBy(['customer' => $customer, 'amount' => intval($limit)]);

        if (!$usageLimit instanceof UsageLimit) {
            var_dump($this->getJsonContent());
            throw new \Exception('Usage Limit not found');
        }
    }

    /**
     * @Then there should not be a limit to warn at :limit for :customerEmail
     */
    public function thereShouldNotBeALimitToWarnAtFor($limit, $customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $usageLimit = $this->usageLimitRepository->findOneBy(['customer' => $customer, 'amount' => intval($limit)]);

        if ($usageLimit instanceof UsageLimit) {
            throw new \Exception('Usage Limit found');
        }
    }

    /**
     * @Given there should be a usage limits for :arg1:
     */
    public function thereShouldBeAUsageLimitsFor($customerEmail, TableNode $table): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        foreach ($table->getColumnsHash() as $row) {
            $warnLevel = $this->getLevel($row['Warning Type']);
            $usageLimit = new UsageLimit();
            $usageLimit->setCustomer($customer);
            $usageLimit->setAmount(intval($row['Amount']));
            $usageLimit->setWarningLevel($warnLevel);

            $this->usageLimitRepository->getEntityManager()->persist($usageLimit);
        }
        $this->usageLimitRepository->getEntityManager()->flush();
    }

    /**
     * @When I delete the usage limit for :limit for :customerEmail
     */
    public function iDeleteTheUsageLimitForFor($limit, $customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $usageLimit = $this->usageLimitRepository->findOneBy(['customer' => $customer, 'amount' => intval($limit)]);

        if (!$usageLimit instanceof UsageLimit) {
            throw new \Exception('Usage Limit not found');
        }

        $this->sendJsonRequest('POST', sprintf('/app/customer/%s/usage-limit/%s/delete', $customer->getId(), $usageLimit->getId()));
    }

    public function getLevel($warningType): WarningLevel
    {
        $warnLevel = match ($warningType) {
            'Disable' => WarningLevel::DISABLE,
            'Warn' => WarningLevel::WARNING,
            default => WarningLevel::NO_WARNING,
        };

        return $warnLevel;
    }

    /**
     * @When I request the usage limits for customer :arg1
     */
    public function iRequestTheUsageLimitsForCustomer($customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $this->sendJsonRequest('GET', sprintf('/api/v1/customer/%s/usage-limits', $customer->getId()));
    }

    /**
     * @Then there should be a usage limits list response should include a warn level limit for :arg1
     */
    public function thereShouldBeAUsageLimitsListResponseShouldIncludeAWarnLevelLimitFor(int $amount): void
    {
        $response = $this->getJsonContent();

        foreach ($response['data'] as $limit) {
            if ($limit['amount'] === $amount) {
                return;
            }
        }

        throw new \Exception('Limit not found');
    }
}
