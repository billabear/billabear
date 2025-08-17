<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Entity\BrandSettings;

interface NewSubscriptionStatsRepositoryInterface
{
    /**
     * Get the count of existing subscriptions for a specific month.
     */
    public function getExistingSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    /**
     * Get the count of new subscriptions for a specific month.
     */
    public function getNewSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    /**
     * Get the count of subscription upgrades for a specific month.
     */
    public function getUpgradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    /**
     * Get the count of subscription downgrades for a specific month.
     */
    public function getDowngradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    /**
     * Get the count of subscription cancellations for a specific month.
     */
    public function getCancellationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    /**
     * Get the count of subscription reactivations for a specific month.
     */
    public function getReactivationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;
}
