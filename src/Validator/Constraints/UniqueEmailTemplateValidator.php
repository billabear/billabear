<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use App\Repository\EmailTemplateRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailTemplateValidator extends ConstraintValidator
{
    public function __construct(private EmailTemplateRepositoryInterface $repository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$value instanceof CreateEmailTemplate) {
            return;
        }

        $emailTemplate = $this->repository->getByNameAndLocaleAndBrand($value->getName(), $value->getLocale(), $value->getBrand());

        if (!$emailTemplate) {
            return;
        }

        $this->context->buildViolation($constraint->message)->atPath('name')->addViolation();
        $this->context->buildViolation($constraint->message)->atPath('locale')->addViolation();
    }
}
