<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Exception\Workflow\InvalidHandlerOptionsException;
use BillaBear\Exception\Workflow\WorkflowTransitionNotSetException;
use Parthenon\Common\Http\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Workflow\Event\Event;

readonly class WebhookTransitionHandler implements DynamicTransitionHandlerInterface
{
    public const string NAME = 'webhook';
    public const string OPTION_METHOD = 'method';
    public const string OPTION_URL = 'url';

    private WorkflowTransition $workflowTransition;

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
            self::OPTION_METHOD => [
                'type' => 'string',
                'required' => true,
            ],
            self::OPTION_URL => [
                'type' => 'string',
                'required' => true,
            ],
        ];
    }

    public function execute(Event $event): void
    {
        $context = [];
        if (method_exists($event, 'getContext')) {
            $context = $event->getContext();
        }

        if (!isset($this->workflowTransition)) {
            throw new WorkflowTransitionNotSetException();
        }
        $options = $this->workflowTransition->getHandlerOptions();

        foreach ($this->getOptions() as $name => $option) {
            $required = $option['required'] ?? false;
            if ($required && !isset($options[$name])) {
                throw new InvalidHandlerOptionsException(sprintf("Option '%s' is not provided", $name));
            }
        }

        $method = $options[self::OPTION_METHOD];
        $url = $options[self::OPTION_URL];

        $stream = $this->streamFactory->createStream(json_encode($context['content'] ?? []));

        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request->withBody($stream);
        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException(sprintf('Webhook failed with status code %d and body %s', $response->getStatusCode(), $response->getBody()->getContents()));
        }
    }

    public function createCloneWithTransition(WorkflowTransition $transition): DynamicTransitionHandlerInterface
    {
        $newHandler = clone $this;
        $newHandler->workflowTransition = $transition;

        return $newHandler;
    }
}
