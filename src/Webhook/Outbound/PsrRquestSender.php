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

use App\Webhook\Outbound\Payload\PayloadInterface;
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

        $response = $this->client->sendRequest($request);

        return new WebhookResponse($response->getStatusCode(), $response->getBody()->getContents());
    }
}
