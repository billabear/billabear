<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery;

use BillaBear\Entity\InvoiceDeliverySettings;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class DeliveryHandlerProvider
{
    /**
     * @param iterable<DeliveryHandlerInterface> $handlers
     */
    public function __construct(
        #[AutowireIterator('billabear.invoice.delivery_handler')]
        private readonly iterable $handlers,
    ) {
    }

    public function getDeliveryHandler(InvoiceDeliverySettings $invoiceDelivery): DeliveryHandlerInterface
    {
        $type = strtolower($invoiceDelivery->getType());
        $defaultHandler = null;

        foreach ($this->handlers as $handler) {
            if (strtolower($handler->getName()) === $type) {
                return $handler;
            }

            if (strtolower(EmailDeliveryHandler::NAME) === strtolower($handler->getName())) {
                $defaultHandler = $handler;
            }
        }

        // Default to email if no handler is found, or the first handler if email is not available
        if ($defaultHandler) {
            return $defaultHandler;
        }

        // If no handlers at all, return the first one we find
        foreach ($this->handlers as $handler) {
            return $handler;
        }

        throw new \RuntimeException('No delivery handlers available');
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        $names = [];
        foreach ($this->handlers as $handler) {
            $names[] = $handler->getName();
        }

        return $names;
    }
}
