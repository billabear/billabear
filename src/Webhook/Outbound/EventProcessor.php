<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
            $start = microtime(true);
            $response = $this->requestSender->send($endpoint->getUrl(), $payload);

            $eventResponse = new WebhookEventResponse();
            $eventResponse->setEvent($event);
            $eventResponse->setEndpoint($endpoint);
            $eventResponse->setProcessingTime(microtime(true) - $start);
            $eventResponse->setBody($response->body);
            $eventResponse->setStatusCode($response->statusCode);
            $eventResponse->setCreatedAt(new \DateTime());

            $eventResponses[] = $eventResponse;
        }
        $event->setCreatedAt(new \DateTime());
        $event->setResponses($eventResponses);

        $this->webhookEventRepository->save($event);
    }
}
