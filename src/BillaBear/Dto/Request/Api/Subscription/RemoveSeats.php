<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

use BillaBear\Entity\Subscription;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RemoveSeats
{
    #[Assert\NotBlank]
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
