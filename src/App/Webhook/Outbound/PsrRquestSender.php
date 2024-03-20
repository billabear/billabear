<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound;

use App\Webhook\Outbound\Payload\PayloadInterface;
use GuzzleHttp\Exception\BadResponseException;
use Parthenon\Common\Http\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class PsrRquestSender implements RequestSenderInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function send(string $url, PayloadInterface $payload): WebhookResponse
    {
        $request = $this->requestFactory->createRequest('POST', $url);
        $stream = $this->streamFactory->createStream(json_encode($payload->getPayload()));
        $request = $request->withBody($stream);

        try {
            $response = $this->client->sendRequest($request);
        } catch (BadResponseException $exception) {
            return new WebhookResponse($exception->getResponse()->getStatusCode(), $exception->getResponse()->getBody()->getContents());
        }

        return new WebhookResponse($response->getStatusCode(), $response->getBody()->getContents());
    }
}
