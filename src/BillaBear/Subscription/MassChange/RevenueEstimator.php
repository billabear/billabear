<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\MassChange;

use BillaBear\Dto\Response\App\Subscription\MassChange\Estimate;
use BillaBear\Entity\MassSubscriptionChange;
use BillaBear\Entity\Price;
use BillaBear\Repository\SubscriptionRepositoryInterface;

class RevenueEstimator
{
    public function __construct(private SubscriptionRepositoryInterface $subscriptionRepository)
    {
    }

    public function generateEstimateDto(MassSubscriptionChange $change): Estimate
    {
        if (!$change->getTargetPrice() || !$change->getNewPrice()) {
            return new Estimate();
        }

        /** @var Price $newPrice */
        $newPrice = $change->getNewPrice();
        /** @var Price $oldPrice */
        $oldPrice = $change->getTargetPrice();

        $changeAmount = $newPrice->getAsMoney()->minus($oldPrice->getAsMoney());
        $numberOfChanges = $this->subscriptionRepository->countMassChangable($change->getTargetSubscriptionPlan(), $change->getTargetPrice(), $change->getBrandSettings(), $change->getTargetCountry());
        $total = $changeAmount->multipliedBy($numberOfChanges);

        if ('day' == $newPrice->getSchedule()) {
            $total = $total->multipliedBy(30);
        }

        $schedule = match ($newPrice->getSchedule()) {
            'year' => 'ARR',
            default => 'MRR',
        };

        $estimate = new Estimate();
        $estimate->setAmount($total->getMinorAmount()->toInt());
        $estimate->setCurrency($total->getCurrency());
        $estimate->setSchedule($schedule);

        return $estimate;
    }
}
