<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Price;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

readonly class CreateTier
{
    public function __construct(
        #[Assert\Positive]
        #[Assert\Type('integer')]
        public int $first_unit,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Positive]
        #[Assert\Type('integer')]
        public ?int $last_unit = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\PositiveOrZero]
        public ?float $unit_price = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\PositiveOrZero]
        public ?float $flat_fee = null,
    ) {
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->first_unit > $this->last_unit && !is_null($this->last_unit)) {
            $context->buildViolation("First unit can't be more than the last unit")
                ->atPath('last_unit')
                ->addViolation();
        }
    }
}
