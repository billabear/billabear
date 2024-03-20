<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Customer;

use App\Dto\Response\Api\Customer\Limits as ApiDto;
use App\Dto\Response\App\Customer\Limits as AppDto;
use App\Entity\Customer;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;

class LimitsFactory
{
    /**
     * @param Subscription[] $subscriptions
     */
    public function createApiDto(Customer $customer, array $subscriptions): ApiDto
    {
        list($limits, $features, $userCount) = $this->getVariables($customer, $subscriptions);

        $dto = new ApiDto();
        $dto->setUserCount($userCount);
        $dto->setLimits($limits);
        $dto->setFeatures($features);

        return $dto;
    }

    public function createAppDto(Customer $customer, array $subscriptions): AppDto
    {
        list($limits, $features, $userCount) = $this->getVariables($customer, $subscriptions);

        $dto = new AppDto();
        $dto->setUserCount($userCount);
        $dto->setLimits($limits);
        $dto->setFeatures($features);

        return $dto;
    }

    public function getVariables(Customer $customer, array $subscriptions): array
    {
        $limits = [];
        $features = [];
        $userCount = 0;

        if ($customer->isDisabled()) {
            return [$limits, $features, $userCount];
        }

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            if (!$subscription->getSubscriptionPlan()) {
                continue;
            }
            /** @var SubscriptionPlanLimit $limit */
            foreach ($subscription->getSubscriptionPlan()->getLimits() as $limit) {
                $name = $limit->getSubscriptionFeature()->getName();

                if (!isset($limits[$name])) {
                    $limits[$name] = 0;
                }
                $limits[$name] += $limit->getLimit();
            }
            foreach ($subscription->getSubscriptionPlan()->getFeatures() as $feature) {
                $features[] = $feature->getName();
            }
            $userCount += $subscription->getSubscriptionPlan()->getUserCount();
        }
        $features = array_unique($features);

        return [$limits, $features, $userCount];
    }
}
