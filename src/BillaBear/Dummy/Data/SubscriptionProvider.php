<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Data;

use BillaBear\Entity\Price;
use BillaBear\Entity\Product;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use Doctrine\Common\Collections\ArrayCollection;

class SubscriptionProvider
{
    use CustomerTrait;

    public function createSubscription(): Subscription
    {
        $subscription = new Subscription();
        $subscription->setCustomer($this->buildCustomer());
        $subscription->setSubscriptionPlan($this->buildSubscriptionPlan());
        $subscription->setPrice($this->buildPrice());
        $subscription->setHasTrial(true);
        $subscription->setTrialLengthDays(7);
        $subscription->setValidUntil(new \DateTime('+7 days'));
        $subscription->setPlanName('Dummy Plan');
        $subscription->setAmount(4242);
        $subscription->setCurrency('EUR');

        return $subscription;
    }

    protected function buildSubscriptionPlan(): SubscriptionPlan
    {
        $plan = new SubscriptionPlan();
        $plan->setName('Dummy Plan');
        $plan->setHasTrial(true);
        $plan->setTrialLengthDays(7);
        $plan->setProduct($this->buildProduct());
        $plan->setPublic(false);
        $plan->setCodeName('dummy_plan');
        $plan->setPrices(new ArrayCollection([$this->buildPrice()]));

        return $plan;
    }

    protected function buildPrice(): Price
    {
        $price = new Price();
        $price->setSchedule('month');
        $price->setAmount(4242);
        $price->setCurrency('EUR');
        $price->setProduct($this->buildProduct());

        return $price;
    }

    protected function buildProduct(): Product
    {
        $product = new Product();
        $product->setName('BillaBear');
        $product->setPhysical(false);

        return $product;
    }
}
