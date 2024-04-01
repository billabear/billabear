<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Features;

use Parthenon\Billing\Entity\SubscriptionFeature;

trait FeatureTrait
{
    public function getFeatureByName(string $name): SubscriptionFeature
    {
        $feature = $this->subscriptionFeatureRepository->findOneBy(['name' => $name]);

        if (!$feature instanceof SubscriptionFeature) {
            throw new \Exception('No feature found');
        }

        $this->subscriptionFeatureRepository->getEntityManager()->refresh($feature);

        return $feature;
    }
}
