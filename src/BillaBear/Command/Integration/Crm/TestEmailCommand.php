<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Crm;

use BillaBear\Notification\Email\Data\SubscriptionCreatedEmailData;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'billabear:integration:crm:test-email',
    description: 'Sync customers to crm system'
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private CustomerRepositoryInterface $customerRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private EmailSenderInterface $emailSender,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $customer = $this->customerRepository->getOldestCustomer();
        $subscription = $this->subscriptionRepository->getOldestSubscription();

        $attachment = new Attachment('test.txt', 'Hello World');

        $email = $this->emailBuilder->build($customer, new SubscriptionCreatedEmailData($subscription));
        $email->addAttachment($attachment);
        $this->emailSender->send($email);

        return Command::SUCCESS;
    }
}
