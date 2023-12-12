<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\TransitionHandlers;

use App\Exception\Workflow\InvalidHandlerOptionsException;
use Parthenon\Common\Http\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Workflow\Event\Event;

class WebhookHandler implements DynamicHandlerInterface
{
    public const NAME = 'webhook';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getOptions(): array
    {
        return [
            'method' => [
                'type' => 'string',
                'required' => true,
            ],
            'url' => [
                'type' => 'string',
                'required' => true,
            ],
        ];
    }

    public function execute(Event $event): void
    {
        $context = $event->getContext();

        if (!isset($context['options'])) {
            throw new InvalidHandlerOptionsException('No options set');
        }
        $options = $context['options'];

        foreach ($this->getOptions() as $name => $option) {
            $required = $option['required'] ?? false;
            if ($required && !isset($options[$name])) {
                throw new InvalidHandlerOptionsException(sprintf("Option '%s' is not provided", $name));
            }
        }

        $method = $options['method'];
        $url = $options['url'];

        $stream = $this->streamFactory->createStream(json_encode($context['content'] ?? []));

        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request->withBody($stream);
        $this->client->sendRequest($request);
    }
}
