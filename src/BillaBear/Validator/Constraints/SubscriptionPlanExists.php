<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class SubscriptionPlanExists extends Constraint
{
    public const DOES_NOT_EXIST = '324hi23kjr-6b9b-41cd-a99e-48dsfasdff';

    protected const ERROR_NAMES = [
        self::DOES_NOT_EXIST => 'DOES_NOT_EXIST',
    ];

    public $message = 'Subscription plan does not exist';
}
