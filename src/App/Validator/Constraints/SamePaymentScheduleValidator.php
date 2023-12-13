<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\Invoice\CreateInvoiceSubscription;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SamePaymentScheduleValidator extends ConstraintValidator
{
    public function __construct(private PriceRepositoryInterface $priceRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!is_array($value)) {
            return;
        }

        $currency = null;
        $schedule = null;

        /** @var CreateInvoiceSubscription $subscription */
        foreach ($value as $subscription) {
            /** @var Price $price */
            $price = $this->priceRepository->getById($subscription->getPrice());
            if (null === $currency) {
                $currency = $price->getCurrency();
            }

            if (null === $schedule) {
                $schedule = $price->getSchedule();
            }

            if ($schedule !== $price->getSchedule()) {
                $this->context->buildViolation($constraint->message)->addViolation();
                break;
            }

            if ($currency !== $price->getCurrency()) {
                $this->context->buildViolation($constraint->currencyMessage)->addViolation();
                break;
            }
        }
    }
}
