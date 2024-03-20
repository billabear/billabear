<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
