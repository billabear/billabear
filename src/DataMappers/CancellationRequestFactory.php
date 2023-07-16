<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Request\Api\Subscription\CancelSubscription as ApiDto;
use App\Dto\Request\App\CancelSubscription as AppDto;
use App\Entity\CancellationRequest;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Subscription;

class CancellationRequestFactory
{
    public function getCancellationRequestEntity(Subscription $subscription, AppDto|ApiDto $dto, BillingAdminInterface $user = null): CancellationRequest
    {
        $cancellationRequest = new CancellationRequest();
        $cancellationRequest->setSubscription($subscription);
        if ($user) {
            $cancellationRequest->setBillingAdmin($user);
        }
        $cancellationRequest->setCreatedAt(new \DateTime());
        $cancellationRequest->setWhen($dto->getWhen());
        $cancellationRequest->setSpecificDate(new \DateTime($dto->getDate()));
        $cancellationRequest->setRefundType($dto->getRefundType());
        $cancellationRequest->setComment($dto->getComment());
        $cancellationRequest->setOriginalValidUntil($subscription->getValidUntil());
        $cancellationRequest->setState('started');

        return $cancellationRequest;
    }
}
