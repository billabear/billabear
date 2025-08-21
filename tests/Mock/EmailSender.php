<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Mock;

use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;

class EmailSender implements EmailSenderInterface
{
    protected array $emails = [];

    public function send(EmailInterface $message)
    {
        $this->emails[] = $message;
    }

    /**
     * @return EmailInterface[]
     */
    public function getEmails(): array
    {
        return $this->emails;
    }
}
