<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Integrations\Crm\Messenger\LogEmail;
use Parthenon\Notification\EmailInterface;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDecorator(decorates: EmailSenderInterface::class)]
class EmailLogger implements EmailSenderInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        #[AutowireDecorated]
        private EmailSenderInterface $inner,
    ) {
    }

    /**
     * @param Email $message
     */
    public function send(EmailInterface $message)
    {
        if ($message instanceof Email) {
            $this->messageBus->dispatch(new LogEmail($message));
        }
        $this->inner->send($message);
    }
}
