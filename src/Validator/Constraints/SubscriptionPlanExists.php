<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Validator\Constraints;

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
