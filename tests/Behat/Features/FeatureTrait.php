<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Features;

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
