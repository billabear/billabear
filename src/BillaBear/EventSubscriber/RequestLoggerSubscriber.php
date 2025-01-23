<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestLoggerSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public const PASSWORD_KEY = 'password';

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
            'kernel.response' => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $data = json_decode($request->getContent(), true);
        if (is_array($data)) {
            $data = $this->filter($data);
            $data = json_encode($data);
        }

        $this->getLogger()->info('Received request', [
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
            'body' => $data,
        ]);
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        // Turn non json bodies to null
        $body = json_encode(json_decode($response->getContent(), true));

        $this->getLogger()->info('Sending response', ['body' => $body, 'status' => $response->getStatusCode()]);
    }

    private function filter(array $items): array
    {
        foreach ($items as $key => $item) {
            if (self::PASSWORD_KEY === strtolower($key)) {
                $items[$key] = '****';
            } elseif (is_array($item)) {
                $items[$key] = $this->filter($item);
            }
        }

        return $items;
    }
}
