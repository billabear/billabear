<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Dto\Generic\Api\Feature as FeatureApi;
use BillaBear\Dto\Generic\Api\Limit as LimitApi;
use BillaBear\Dto\Response\Api\Customer\Limits as ApiDto;
use BillaBear\Dto\Response\App\Customer\Limits as AppDto;
use BillaBear\Entity\Customer;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;

class LimitsFactory
{
    /**
     * @param Subscription[] $subscriptions
     */
    public function createApiDto(Customer $customer, array $subscriptions): ApiDto
    {
        list($limits, $features, $userCount) = $this->getApiVariables($customer, $subscriptions);

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

    public function getApiVariables(Customer $customer, array $subscriptions): array
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
                $feature = $this->buildFeature($limit->getSubscriptionFeature());
                $name = $limit->getSubscriptionFeature()->getName();

                if (!isset($limits[$name])) {
                    $limits[$name] = new LimitApi();
                    $limits[$name]->setFeature($feature);
                }
                $limits[$name]->setLimit($limits[$name]->getLimit() + $limit->getLimit());
            }
            foreach ($subscription->getSubscriptionPlan()->getFeatures() as $feature) {
                $features[$feature->getCode()] = $this->buildFeature($feature);
            }
            $userCount += $subscription->getSubscriptionPlan()->getUserCount();
        }
        $limits = array_values($limits);
        $features = array_values($features);

        return [$limits, $features, $userCount];
    }

    private function buildFeature(SubscriptionFeature $feature): FeatureApi
    {
        $dto = new FeatureApi();
        $dto->setName($feature->getName());
        $dto->setDescription($feature->getDescription());
        $dto->setCode($feature->getCode());

        return $dto;
    }
}
