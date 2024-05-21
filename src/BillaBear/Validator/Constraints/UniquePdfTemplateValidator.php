<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Dto\Request\App\Template\CreatePdfTemplate;
use BillaBear\Repository\TemplateRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniquePdfTemplateValidator extends ConstraintValidator
{
    public function __construct(private TemplateRepositoryInterface $repository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$value instanceof CreatePdfTemplate) {
            return;
        }

        if (!$value->getType() || !$value->getLocale() || !$value->getBrand()) {
            return;
        }
        $emailTemplate = $this->repository->getByNameAndLocaleAndBrand($value->getType(), $value->getLocale(), $value->getBrand());

        if (!$emailTemplate) {
            return;
        }

        $this->context->buildViolation($constraint->message)->atPath('name')->addViolation();
        $this->context->buildViolation($constraint->message)->atPath('locale')->addViolation();
    }
}
