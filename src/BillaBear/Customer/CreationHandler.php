<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Customer\Messenger\CustomerEvent;
use BillaBear\Customer\Messenger\CustomerEventType;
use BillaBear\Entity\Customer;
use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Event\Customer\CustomerCreated as CustomerCreatedEvent;
use BillaBear\Invoice\Delivery\EmailDeliveryHandler;
use BillaBear\Invoice\InvoiceFormat;
use BillaBear\Notification\Slack\Data\CustomerCreated as CustomerCreatedSlack;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceDeliverySettingsRepositoryInterface;
use BillaBear\Stats\CustomerCreationStats;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerCreatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Obol\Exception\ProviderFailureException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreationHandler
{
    public function __construct(
        private NotificationSender $notificationSender,
        private WebhookDispatcherInterface $eventProcessor,
        private ExternalRegisterInterface $externalRegister,
        private CustomerRepositoryInterface $customerRepository,
        private CustomerCreationStats $customerCreationStats,
        private InvoiceDeliverySettingsRepositoryInterface $invoiceDeliverySettingsRepository,
        private EventDispatcherInterface $eventDispatcher,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ProviderFailureException|\Throwable
     */
    public function handleCreation(Customer $customer): void
    {
        if (!$customer->hasExternalsCustomerReference()) {
            $this->externalRegister->register($customer);
        }
        $this->customerCreationStats->handleStats($customer);
        $this->customerRepository->save($customer);

        $this->eventProcessor->dispatch(new CustomerCreatedPayload($customer));
        $this->handleSlackNotifications($customer);
        $this->handleExtraEntities($customer);
        $this->eventDispatcher->dispatch(new CustomerCreatedEvent($customer), CustomerCreatedEvent::NAME);
        $this->messageBus->dispatch(new CustomerEvent(CustomerEventType::CREATION, (string) $customer->getId()));
    }

    public function handleSlackNotifications(Customer $customer): void
    {
        $notificationMessage = new CustomerCreatedSlack($customer);
        $this->notificationSender->sendNotification($notificationMessage);
    }

    private function handleExtraEntities(Customer $customer): void
    {
        $invoiceDelivery = new InvoiceDeliverySettings();
        $invoiceDelivery->setCustomer($customer);
        $invoiceDelivery->setEnabled(true);
        $invoiceDelivery->setCreatedAt(new \DateTime());
        $invoiceDelivery->setUpdatedAt(new \DateTime());
        $invoiceDelivery->setInvoiceFormat(InvoiceFormat::PDF->value);
        $invoiceDelivery->setType(EmailDeliveryHandler::NAME);

        $this->invoiceDeliverySettingsRepository->save($invoiceDelivery);
    }
}
