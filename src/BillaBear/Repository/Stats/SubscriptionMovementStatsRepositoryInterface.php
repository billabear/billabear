<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Entity\BrandSettings;

interface SubscriptionMovementStatsRepositoryInterface
{
    public function getExistingSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    public function getNewSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    public function getUpgradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    public function getDowngradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    public function getCancellationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;

    public function getReactivationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int;
}
