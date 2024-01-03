<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\BrandSettings;
use App\Entity\Price;
use App\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\Subscription;

/**
 * @method findById($id) \App\Entity\Subscription
 */
interface SubscriptionRepositoryInterface extends \Parthenon\Billing\Repository\SubscriptionRepositoryInterface
{
    /**
     * @return Subscription[]
     */
    public function getSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return Subscription[]
     */
    public function getInvoiceSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return Subscription[]
     */
    public function getAll(): array;

    /**
     * @return Subscription[]
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
     * @return Subscription[]
     */
    public function findMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): array;

    public function countMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): int;

    /**
     * @return Subscription[]
     */
    public function findActiveSubscriptionsOnDate(\DateTime $dateTime, int $count): array;
}
