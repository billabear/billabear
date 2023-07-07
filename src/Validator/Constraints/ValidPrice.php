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

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidPrice extends Constraint
{
    public const NOT_UNIQUE_ERROR = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';

    protected const ERROR_NAMES = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public $message = 'No valid price found';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
