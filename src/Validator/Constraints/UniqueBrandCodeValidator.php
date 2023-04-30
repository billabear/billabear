<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Validator\Constraints;

use App\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueBrandCodeValidator extends ConstraintValidator
{
    public function __construct(private BrandSettingsRepositoryInterface $brandSettingRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            $this->brandSettingRepository->getByCode($value);
        } catch (NoEntityFoundException $exception) {
            return;
        }

        $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
    }
}
