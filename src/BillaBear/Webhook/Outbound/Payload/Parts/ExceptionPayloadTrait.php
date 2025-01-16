<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
                'body' => $exception->getRequest()->getBody()->getContents(),
            ];

            $output['response'] = [
                'status' => $exception->getResponse()->getStatusCode(),
                'body' => $exception->getResponse()->getBody()->getContents(),
            ];
        }

        return $output;
    }
}
