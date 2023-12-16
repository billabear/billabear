<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\TransitionHandlers;

use App\Entity\WorkflowTransition;
use App\Exception\Workflow\InvalidHandlerOptionsException;
use App\Exception\Workflow\WorkflowTransitionNotSetException;
use Parthenon\Common\Http\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Workflow\Event\Event;

class WebhookHandler implements DynamicHandlerInterface
{
    public const NAME = 'webhook';
    public const OPTION_METHOD = 'method';
    public const OPTION_URL = 'url';
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
        $context = $event->getContext();

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
        $this->client->sendRequest($request);
    }

    public function createCloneWithTransition(WorkflowTransition $transition): DynamicHandlerInterface
    {
        $newHandler = clone $this;
        $newHandler->workflowTransition = $transition;

        return $newHandler;
    }
}
