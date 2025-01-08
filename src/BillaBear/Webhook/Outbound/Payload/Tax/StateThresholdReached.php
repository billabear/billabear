<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Tax;

use BillaBear\Entity\State;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class StateThresholdReached implements PayloadInterface
{
    public function __construct(private State $state)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::TAX_COUNTRY_THRESHOLD_REACHED;
    }

    public function getPayload(): array
    {
        return [
            'type' => $this->getType()->value,
            'country' => [
                'name' => $this->state->getCountry()->getName(),
                'code' => $this->state->getCountry()->getIsoCode(),
                'threshold' => (string) $this->state->getCountry()->getThresholdAsMoney()->getAmount(),
            ],
            'state' => [
                'name' => $this->state->getName(),
                'threshold' => (string) $this->state->getThresholdAsMoney()->getAmount(),
            ],
        ];
    }
}
