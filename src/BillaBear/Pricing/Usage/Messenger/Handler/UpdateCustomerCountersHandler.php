<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage\Messenger\Handler;

use BillaBear\Pricing\Usage\Messenger\Message\UpdateCustomerCounters;
use BillaBear\Pricing\Usage\MetricCounterUpdater;
use BillaBear\Pricing\Usage\Warning\WarningCheck;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateCustomerCountersHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly MetricCounterUpdater $metricCounterUpdater,
        private readonly UsageLimitRepositoryInterface $usageLimitRepository,
        private readonly WarningCheck $warningCheck,
    ) {
    }

    public function __invoke(UpdateCustomerCounters $message): void
    {
        $this->getLogger()->info('Starting updating metric counters for customer', ['customer_id' => $message->customerId]);
        $customer = $this->customerRepository->findById($message->customerId);
        $subscriptions = $this->subscriptionRepository->getAllActiveForCustomer($customer);
        foreach ($subscriptions as $subscription) {
            $this->metricCounterUpdater->updateForSubscription($subscription);
        }

        $this->warningCheck->check($customer, $subscriptions);
    }
}
