<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Parts;

use BillaBear\Entity\SubscriptionPlan;

trait PlanPayloadTrait
{
    protected function createPlanPayload(SubscriptionPlan $plan): array
    {
        return [
            'id' => (string) $plan->getId(),
            'name' => $plan->getName(),
            'code' => $plan->getCodeName(),
            'has_trial' => $plan->getHasTrial(),
            'trial_length_in_days' => $plan->getTrialLengthDays(),
            'standalone_trial' => $plan->getIsTrialStandalone(),
        ];
    }
}
