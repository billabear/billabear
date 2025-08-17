<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Repository\Stats\NewSubscriptionStatsRepositoryInterface;
use Parthenon\Athena\Repository\DoctrineCrudRepository;

class NewSubscriptionStatsRepository extends DoctrineCrudRepository implements NewSubscriptionStatsRepositoryInterface
{
    public function getExistingSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getExistingSubscriptionsCountForMonth($month, $brandSettings);
    }

    public function getNewSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getNewSubscriptionsCountForMonth($month, $brandSettings);
    }

    public function getUpgradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getUpgradesCountForMonth($month, $brandSettings);
    }

    public function getDowngradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getDowngradesCountForMonth($month, $brandSettings);
    }

    public function getCancellationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getCancellationsCountForMonth($month, $brandSettings);
    }

    public function getReactivationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->entityRepository->getReactivationsCountForMonth($month, $brandSettings);
    }
}
