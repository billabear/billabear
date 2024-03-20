<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class PaymentMethodExists extends Constraint
{
    public const DOES_NOT_EXIST = '23bd9dbf-6b9b-41cd-a99e-48dsfasdff';

    protected const ERROR_NAMES = [
        self::DOES_NOT_EXIST => 'DOES_NOT_EXIST',
    ];

    public $message = 'Payment details do not exist';
}
