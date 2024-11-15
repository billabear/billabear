<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Entity\Customer;
use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Enum\InvoiceDeliveryType;
use BillaBear\Enum\InvoiceFormat;
use BillaBear\Notification\Slack\Data\CustomerCreated;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceDeliverySettingsRepositoryInterface;
use BillaBear\Stats\CustomerCreationStats;
use BillaBear\Webhook\Outbound\Payload\CustomerCreatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class CreationHandler
{
    public function __construct(
        private NotificationSender $notificationSender,
        private WebhookDispatcherInterface $eventProcessor,
        private ExternalRegisterInterface $externalRegister,
        private CustomerRepositoryInterface $customerRepository,
        private CustomerCreationStats $customerCreationStats,
        private InvoiceDeliverySettingsRepositoryInterface $invoiceDeliverySettingsRepository,
    ) {
    }

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
    }

    public function handleSlackNotifications(Customer $customer): void
    {
        $notificationMessage = new CustomerCreated($customer);
        $this->notificationSender->sendNotification($notificationMessage);
    }

    private function handleExtraEntities(Customer $customer): void
    {
        $invoiceDelivery = new InvoiceDeliverySettings();
        $invoiceDelivery->setCustomer($customer);
        $invoiceDelivery->setEnabled(true);
        $invoiceDelivery->setCreatedAt(new \DateTime());
        $invoiceDelivery->setUpdatedAt(new \DateTime());
        $invoiceDelivery->setInvoiceFormat(InvoiceFormat::PDF);
        $invoiceDelivery->setType(InvoiceDeliveryType::EMAIL);

        $this->invoiceDeliverySettingsRepository->save($invoiceDelivery);
    }
}
