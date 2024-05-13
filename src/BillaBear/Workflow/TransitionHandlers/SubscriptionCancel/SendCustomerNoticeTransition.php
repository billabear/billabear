<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCancel;

use BillaBear\Entity\CancellationRequest;
use BillaBear\Entity\Customer;
use BillaBear\Notification\Email\Data\SubscriptionCancelEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNoticeTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private EmailSenderInterface $emailSender,
        private EmailBuilder $builder,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var CancellationRequest $cancellationRequest */
        $cancellationRequest = $event->getSubject();

        if (!$cancellationRequest instanceof CancellationRequest) {
            $this->getLogger()->error('Cancellation Request transition has something other than a CancellationRequest object');

            return;
        }
        /** @var Customer $customer */
        $customer = $cancellationRequest->getSubscription()->getCustomer();

        if (!$customer->getBrandSettings()->getNotificationSettings()->getSubscriptionCreation()) {
            $this->getLogger()->info('Brand has subscription cancellation email');

            return;
        }

        $this->getLogger()->info('Starting customer notice transition');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNotificationSettings()?->getSendCustomerNotifications()) {
            $this->getLogger()->info('Starting customer notifications are disabled in the settings');

            return;
        }

        $emailData = new SubscriptionCancelEmail($cancellationRequest->getSubscription());
        $email = $this->builder->build($cancellationRequest->getSubscription()->getCustomer(), $emailData);
        $this->emailSender->send($email);
        $this->getLogger()->info('Sent customer notice', ['sender' => get_class($this->emailSender)]);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancel_subscription.transition.send_customer_notice' => ['transition'],
        ];
    }
}
