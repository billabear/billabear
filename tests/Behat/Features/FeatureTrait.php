<?php

/*
 * Copyright all rights reserved. No public license given.
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
