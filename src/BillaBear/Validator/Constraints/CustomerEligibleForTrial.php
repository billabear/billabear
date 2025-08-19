<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class CustomerEligibleForTrial extends Constraint
{
    public const ALREADY_USED_TRIAL = '324hi23kjr-6b9b-41cd-a99e-48dsfasdff';

    protected const ERROR_NAMES = [
        self::ALREADY_USED_TRIAL => 'ALREADY_USED_TRIAL',
    ];

    public $message = 'Customer has already used a trial for this subscription plan';
}
