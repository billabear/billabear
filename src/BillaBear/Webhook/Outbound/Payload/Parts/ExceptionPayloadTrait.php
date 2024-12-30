<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Parts;

use GuzzleHttp\Exception\RequestException;

trait ExceptionPayloadTrait
{
    protected function convertException(\Exception $exception): array
    {
        $output = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if ($exception instanceof RequestException) {
            $output['request'] = [
                'method' => $exception->getRequest()->getMethod(),
                'uri' => $exception->getRequest()->getUri(),
                'headers' => $exception->getRequest()->getHeaders(),
                'body' => $exception->getRequest()->getBody()->getContents(),
            ];

            $output['response'] = [
                'status' => $exception->getResponse()->getStatusCode(),
                'headers' => $exception->getResponse()->getHeaders(),
                'body' => $exception->getResponse()->getBody()->getContents(),
            ];
        }

        return $output;
    }
}
