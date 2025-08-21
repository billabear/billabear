<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Invoice\Delivery\Messenger\InvoiceDeliveryRequest;
use BillaBear\Repository\InvoiceDeliverySettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNotificationsTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.invoice_process.transition.send_customer_notifications' => ['transition'],
        ];
    }

    public function transition(Event $event)
    {
        /** @var InvoiceProcess $invoiceProcess */
        $invoiceProcess = $event->getSubject();
        $invoice = $invoiceProcess->getInvoice();
        $deliveries = $this->invoiceDeliveryRepository->getEnabledForCustomer($invoice->getCustomer());

        foreach ($deliveries as $delivery) {
            $this->getLogger()->info(
                'Sending invoice to delivery queue',
                [
                    'invoice_id' => (string) $invoice->getId(),
                    'invoice_delivery_id' => (string) $delivery->getId(),
                ]
            );

            $request = new InvoiceDeliveryRequest((string) $invoice->getId(), (string) $delivery->getId());
            $this->messageBus->dispatch($request);
        }
    }
}
