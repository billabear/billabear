<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Messenger;

use BillaBear\Notification\Email\Email;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class LogEmail
{
    public function __construct(
        public Email $email,
    ) {
    }
}
