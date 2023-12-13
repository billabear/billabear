<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Workflow\TransitionHandlers;

use App\Exception\Workflow\InvalidHandlerOptionsException;
use App\Workflow\TransitionHandlers\WebhookHandler;
use Parthenon\Common\Http\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Workflow\Event\Event;

class WebhookHandlerTest extends TestCase
{
    public function testChecksOptionsExist()
    {
        $this->expectException(InvalidHandlerOptionsException::class);

        $client = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getContext')->willReturn([]);

        $subject = new WebhookHandler($client, $requestFactory, $streamFactory);
        $subject->execute($event);
    }

    public function testChecksOptionsAreGivenMethod()
    {
        $this->expectException(InvalidHandlerOptionsException::class);
        $this->expectExceptionMessage("Option 'method' is not provided");

        $client = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getContext')->willReturn(['options' => []]);

        $subject = new WebhookHandler($client, $requestFactory, $streamFactory);
        $subject->execute($event);
    }

    public function testChecksOptionsAreGivenUrl()
    {
        $this->expectException(InvalidHandlerOptionsException::class);
        $this->expectExceptionMessage("Option 'url' is not provided");

        $client = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getContext')->willReturn(['options' => ['method' => 'post']]);

        $subject = new WebhookHandler($client, $requestFactory, $streamFactory);
        $subject->execute($event);
    }

    public function testSendRequest()
    {
        $method = 'POST';
        $url = 'http://example.org/';

        $client = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getContext')->willReturn(['options' => ['method' => $method, 'url' => $url], 'content' => []]);

        $stream = $this->createMock(StreamInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        $request = $this->createMock(RequestInterface::class);
        $request->method('withBody')->with($stream)->willReturn($request);

        $requestFactory->method('createRequest')->with($method, $url)->willReturn($request);

        $client->expects($this->once())->method('sendRequest')->with($request);

        $subject = new WebhookHandler($client, $requestFactory, $streamFactory);
        $subject->execute($event);
    }
}
