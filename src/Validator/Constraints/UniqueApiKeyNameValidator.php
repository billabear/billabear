<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Validator\Constraints;

use App\Repository\ApiKeyRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueApiKeyNameValidator extends ConstraintValidator
{
    public function __construct(private ApiKeyRepositoryInterface $apiKeyRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if ($this->apiKeyRepository->hasApiKeyForName($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }
    }
}
