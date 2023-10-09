<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\Api\Subscription;

use App\Entity\Subscription;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RemoveSeats
{
    #[Assert\NotBlank()]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $seats;

    #[Ignore]
    private Subscription $subscription;

    public function getSeats()
    {
        return $this->seats;
    }

    public function setSeats($seats): void
    {
        $this->seats = $seats;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    #[Assert\Callback]
    public function validNumberOfSeats(ExecutionContextInterface $context, mixed $payload)
    {
        if ($this->seats >= $this->subscription->getSeats()) {
            $context->buildViolation('Too many seats')->atPath('seats')->addViolation();
        }
    }
}
