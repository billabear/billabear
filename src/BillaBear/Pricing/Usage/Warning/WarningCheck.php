<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage\Warning;

use BillaBear\Customer\Disabler;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\UsageWarning;
use BillaBear\Pricing\Usage\CostEstimator;
use BillaBear\Pricing\Usage\WarningLevel;
use BillaBear\Repository\Usage\UsageWarningRepositoryInterface;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Usage\UsageWarningTriggeredPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Brick\Money\Money;
use Parthenon\Common\LoggerAwareTrait;

class WarningCheck
{
    use LoggerAwareTrait;

    public function __construct(
        private CostEstimator $costEstimator,
        private UsageLimitRepositoryInterface $usageLimitRepository,
        private UsageWarningRepositoryInterface $usageWarningRepository,
        private Disabler $disabler,
        private WarningNotifier $warningNotifier,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    /**
     * @param Subscription[] $subscriptions
     */
    public function check(Customer $customer, array $subscriptions): void
    {
        $this->getLogger()->info('Checking if usage limits have been reached for customer', ['customer_id' => (string) $customer->getId()]);

        if (empty($subscriptions)) {
            $this->getLogger()->info('Customer does not have any subscriptions to check usage limits for', ['customer_id' => (string) $customer->getId()]);

            return;
        }

        $limits = $this->usageLimitRepository->getForCustomer($customer);
        if (empty($limits)) {
            $this->getLogger()->info('Customer does not have any usage limits to check', ['customer_id' => (string) $customer->getId()]);

            return;
        }

        /** @var \DateTime $endOfPeriod */
        $endOfPeriod = null;
        /** @var \DateTime $startOfPeriod */
        $startOfPeriod = null;
        foreach ($subscriptions as $subscription) {
            if ($subscription->getValidUntil()->getTimestamp() > $endOfPeriod?->getTimestamp()) {
                $endOfPeriod = $subscription->getValidUntil();
            }

            if ($subscription->getStartOfCurrentPeriod()?->getTimestamp() > $startOfPeriod?->getTimestamp()) {
                $startOfPeriod = $subscription->getStartOfCurrentPeriod();
            }
        }

        $costEstimate = $this->costEstimator->getTotalEstimate($subscriptions);

        foreach ($limits as $limit) {
            if ($this->usageWarningRepository->hasOneForUsageLimitAndDates($limit, $startOfPeriod, $endOfPeriod)) {
                // Already warned
                continue;
            }

            $limitAmount = Money::ofMinor($limit->getAmount(), $costEstimate->getCurrency());
            if (!$costEstimate->isGreaterThan($limitAmount)) {
                // Not Reached so continue
                continue;
            }

            if (WarningLevel::DISABLE === $limit->getWarningLevel()) {
                $this->disabler->disable($customer);
                $this->warningNotifier->notifyOfDisable($customer, $limit, $costEstimate);
            } elseif (WarningLevel::WARNING === $limit->getWarningLevel()) {
                $this->warningNotifier->notifyOfWarning($customer, $limit, $costEstimate);
            }

            $warning = new UsageWarning();
            $warning->setCustomer($customer);
            $warning->setUsageLimit($limit);
            $warning->setStartOfPeriod($startOfPeriod);
            $warning->setEndOfPeriod($endOfPeriod);
            $warning->setCreatedAt(new \DateTime());

            $this->usageWarningRepository->save($warning);

            $this->webhookDispatcher->dispatch(new UsageWarningTriggeredPayload($warning));
        }
    }
}
