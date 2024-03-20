<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\Subscription\MassChange\CreateMassChange;
use App\Dto\Request\App\Subscription\MassChange\EstimateMassChange;
use App\Entity\Price;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPriceChangeValidator extends ConstraintValidator
{
    public function __construct(private PriceRepositoryInterface $priceRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateMassChange && !$value instanceof EstimateMassChange) {
            return;
        }
        if (!Uuid::isValid((string) $value->getTargetPrice())) {
            return;
        }
        if (!Uuid::isValid((string) $value->getNewPrice())) {
            return;
        }

        if ($this->context->getViolations()->count() > 0) {
            return;
        }
        try {
            /** @var Price $targetPrice */
            $targetPrice = $this->priceRepository->findById($value->getTargetPrice());
            /** @var Price $newPrice */
            $newPrice = $this->priceRepository->findById($value->getNewPrice());

            if ($newPrice->getCurrency() != $targetPrice->getCurrency() || $newPrice->getSchedule() != $targetPrice->getSchedule()) {
                $this->context->buildViolation($constraint->message)->atPath('targetPrice')->addViolation();
                $this->context->buildViolation($constraint->message)->atPath('newPrice')->addViolation();
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
