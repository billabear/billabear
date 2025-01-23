<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Messenger;

use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class DelayMiddleware implements MiddlewareInterface
{
    use LoggerAwareTrait;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $last = $envelope->last(DelayStamp::class);
        if (null !== $last) {
            // Set somewhere else so we'll respect it.
            $this->getLogger()->info('Respecting delay stamp');

            return $stack->next()->handle($envelope, $stack);
        }

        $this->getLogger()->info('Adding delay stamp');
        $envelope = $envelope->with(new DelayStamp(5000));

        return $stack->next()->handle($envelope, $stack);
    }
}
