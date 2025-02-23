<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Price;
use BillaBear\Entity\Subscription as BillaSubscription;
use BillaBear\Entity\SubscriptionPlan;
use Parthenon\Athena\ResultSet;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;

/**
 * @method BillaSubscription   findById($id)
 * @method BillaSubscription[] getAllActiveForCustomer(CustomerInterface $customer)
 */
interface SubscriptionRepositoryInterface extends \Parthenon\Billing\Repository\SubscriptionRepositoryInterface
{
    /**
     * @return BillaSubscription[]
     */
    public function getSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return BillaSubscription[]
     */
    public function getSubscriptionsExpiringInTwoDays(): array;

    /**
     * @return BillaSubscription[]
     */
    public function getTrialEndingInNextSevenDays(): array;

    /**
     * @return BillaSubscription[]
     */
    public function getInvoiceSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return BillaSubscription[]
     */
    public function getAll(): array;

    /**
     * @return BillaSubscription[]
     */
    public function getAllActive(): array;

    public function getCountActive(): int;

    public function getActiveCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int;

    public function getCreatedCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int;

    public function getOldestSubscription(): Subscription;

    public function getPlanCounts(): array;

    public function getScheduleCounts(): array;

    public function getCountOfActiveCustomers(): int;

    /**
     * @return BillaSubscription[]
     */
    public function findMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): array;

    public function countMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): int;

    /**
     * @return BillaSubscription[]
     */
    public function findActiveSubscriptionsOnDate(\DateTime $dateTime, int $count): array;

    public function getAllActiveCountForCustomer(CustomerInterface $customer): int;

    public function getAllCancelledCountForCustomer(CustomerInterface $customer): int;

    public function getLastTenForCustomer($customer): ResultSet;

    public function getSubscriptionWithUsage(): array;
}
