<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Integrations\Crm\Messenger\LogEmail;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailLogger
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function logEmail(Customer $customer, EmailTemplate $emailTemplate): void
    {
        $this->messageBus->dispatch(new LogEmail($customer->getId(), $emailTemplate->getName()));
    }
}
