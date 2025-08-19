<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Invoice\Delivery;

use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Invoice\Delivery\DeliveryHandlerInterface;
use BillaBear\Invoice\Delivery\DeliveryHandlerProvider;
use PHPUnit\Framework\TestCase;

class DeliveryHandlerProviderTest extends TestCase
{
    public function testGetDeliveryHandlerCaseInsensitive(): void
    {
        // Create mock handlers
        $emailHandler = $this->createMock(DeliveryHandlerInterface::class);
        $emailHandler->method('getName')->willReturn('email');

        $sftpHandler = $this->createMock(DeliveryHandlerInterface::class);
        $sftpHandler->method('getName')->willReturn('sftp');

        $webhookHandler = $this->createMock(DeliveryHandlerInterface::class);
        $webhookHandler->method('getName')->willReturn('webhook');

        // Create an array of handlers
        $handlers = [$emailHandler, $sftpHandler, $webhookHandler];

        // Create the provider with the handlers
        $provider = new DeliveryHandlerProvider($handlers);

        // Create a mock InvoiceDeliverySettings with uppercase type
        $invoiceDelivery = $this->createMock(InvoiceDeliverySettings::class);
        $invoiceDelivery->method('getType')->willReturn('SFTP');

        // Test that the provider returns the correct handler despite case difference
        $result = $provider->getDeliveryHandler($invoiceDelivery);
        $this->assertSame($sftpHandler, $result);
    }

    public function testGetDeliveryHandlerMixedCase(): void
    {
        // Create mock handlers
        $emailHandler = $this->createMock(DeliveryHandlerInterface::class);
        $emailHandler->method('getName')->willReturn('email');

        $sftpHandler = $this->createMock(DeliveryHandlerInterface::class);
        $sftpHandler->method('getName')->willReturn('sftp');

        $webhookHandler = $this->createMock(DeliveryHandlerInterface::class);
        $webhookHandler->method('getName')->willReturn('webhook');

        // Create an array of handlers
        $handlers = [$emailHandler, $sftpHandler, $webhookHandler];

        // Create the provider with the handlers
        $provider = new DeliveryHandlerProvider($handlers);

        // Create a mock InvoiceDeliverySettings with mixed case type
        $invoiceDelivery = $this->createMock(InvoiceDeliverySettings::class);
        $invoiceDelivery->method('getType')->willReturn('WebHook');

        // Test that the provider returns the correct handler despite case difference
        $result = $provider->getDeliveryHandler($invoiceDelivery);
        $this->assertSame($webhookHandler, $result);
    }

    public function testDefaultToEmailHandler(): void
    {
        // Create mock handlers
        $emailHandler = $this->createMock(DeliveryHandlerInterface::class);
        $emailHandler->method('getName')->willReturn('email');

        $sftpHandler = $this->createMock(DeliveryHandlerInterface::class);
        $sftpHandler->method('getName')->willReturn('sftp');

        // Create an array of handlers
        $handlers = [$emailHandler, $sftpHandler];

        // Create the provider with the handlers
        $provider = new DeliveryHandlerProvider($handlers);

        // Create a mock InvoiceDeliverySettings with non-existent type
        $invoiceDelivery = $this->createMock(InvoiceDeliverySettings::class);
        $invoiceDelivery->method('getType')->willReturn('nonexistent');

        // Test that the provider defaults to email handler
        $result = $provider->getDeliveryHandler($invoiceDelivery);
        $this->assertSame($emailHandler, $result);
    }

    public function testGetNamesReturnsAllHandlerNames(): void
    {
        // Create mock handlers
        $emailHandler = $this->createMock(DeliveryHandlerInterface::class);
        $emailHandler->method('getName')->willReturn('email');

        $sftpHandler = $this->createMock(DeliveryHandlerInterface::class);
        $sftpHandler->method('getName')->willReturn('sftp');

        $webhookHandler = $this->createMock(DeliveryHandlerInterface::class);
        $webhookHandler->method('getName')->willReturn('webhook');

        // Create an array of handlers
        $handlers = [$emailHandler, $sftpHandler, $webhookHandler];

        // Create the provider with the handlers
        $provider = new DeliveryHandlerProvider($handlers);

        // Test that getNames returns all handler names
        $names = $provider->getNames();
        $this->assertCount(3, $names);
        $this->assertContains('email', $names);
        $this->assertContains('sftp', $names);
        $this->assertContains('webhook', $names);
    }
}
