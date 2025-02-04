<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProcessor implements ProcessorInterface
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(LogRecord $record)
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $record;
        }

        $record->extra['billing_admin_id'] = (string) $user->getId();

        return $record;
    }
}
