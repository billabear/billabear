<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound;

use App\Entity\WebhookEvent;
use App\Entity\WebhookEventResponse;
use App\Repository\WebhookEndpointRepositoryInterface;
use App\Repository\WebhookEventRepositoryInterface;
use App\Webhook\Outbound\Payload\PayloadInterface;

class EventProcessor
{
    public function __construct(
        private RequestSenderInterface $requestSender,
        private WebhookEndpointRepositoryInterface $webhookEndpointRepository,
        private WebhookEventRepositoryInterface $webhookEventRepository,
    ) {
    }

    public function process(PayloadInterface $payload): void
    {
        $endpoints = $this->webhookEndpointRepository->getActive();

        if (empty($endpoints)) {
            return;
        }
        $event = new WebhookEvent();
        $event->setType($payload->getType());
        $event->setPayload(json_encode($payload->getPayload()));
        $eventResponses = [];
        foreach ($endpoints as $endpoint) {
            $eventResponse = new WebhookEventResponse();
            $eventResponse->setEvent($event);
            $eventResponse->setEndpoint($endpoint);
            $eventResponse->setUrl($endpoint->getUrl());

            $start = microtime(true);
            try {
                $response = $this->requestSender->send($endpoint->getUrl(), $payload);
                $eventResponse->setBody($response->body);
                $eventResponse->setStatusCode($response->statusCode);
            } catch (\Throwable $exception) {
                $eventResponse->setErrorMessage($exception->getMessage());
            }

            $eventResponse->setProcessingTime(microtime(true) - $start);
            $eventResponse->setCreatedAt(new \DateTime());

            $eventResponses[] = $eventResponse;
        }
        $event->setCreatedAt(new \DateTime());
        $event->setResponses($eventResponses);

        $this->webhookEventRepository->save($event);
    }
}
