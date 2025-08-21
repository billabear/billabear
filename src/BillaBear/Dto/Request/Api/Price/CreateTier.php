<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Price;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateTier
{
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $first_unit;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $last_unit;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\PositiveOrZero]
    private $unit_price;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\PositiveOrZero]
    private $flat_fee;

    public function getFirstUnit()
    {
        return $this->first_unit;
    }

    public function setFirstUnit($first_unit): void
    {
        $this->first_unit = $first_unit;
    }

    public function getLastUnit()
    {
        return $this->last_unit;
    }

    public function setLastUnit($last_unit): void
    {
        $this->last_unit = $last_unit;
    }

    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    public function setUnitPrice($unit_price): void
    {
        $this->unit_price = $unit_price;
    }

    public function getFlatFee()
    {
        return $this->flat_fee;
    }

    public function setFlatFee($flat_fee): void
    {
        $this->flat_fee = $flat_fee;
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
