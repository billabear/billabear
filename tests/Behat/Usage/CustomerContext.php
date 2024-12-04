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

        $warnLevel = match ($data['Warning Type']) {
            'Disable' => WarningLevel::DISABLED,
            'Warn' => WarningLevel::WARNED,
            default => WarningLevel::NO_WARNING,
        };

        $payload = [
            'amount' => intval($data['Amount']),
            'warn_level' => $warnLevel->value,
        ];

        $this->sendJsonRequest('POST', sprintf('/app/customer/%s/usage-limit', $customer->getId()), $payload);
    }

    /**
     * @Then there should be a limit to warn at :limit for :customerEmail
     */
    public function thereShouldBeALimitToWarnAtFor($limit, $customerEmail): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $usageLimit = $this->usageLimitRepository->findOneBy(['customer' => $customer]);

        if (!$usageLimit instanceof UsageLimit) {
            var_dump($this->getJsonContent());
            throw new \Exception('Usage Limit not found');
        }

        if (intval($limit) !== $usageLimit->getAmount()) {
            throw new \Exception(sprintf('Got %d instead of %d', $usageLimit->getAmount(), $limit));
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
}
