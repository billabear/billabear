<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Tax;

use BillaBear\Entity\Country;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class CountryThresholdReached implements PayloadInterface
{
    public function __construct(private Country $country)
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
                'name' => $this->country->getName(),
                'code' => $this->country->getIsoCode(),
                'threshold' => (string) $this->country->getThresholdAsMoney()->getAmount(),
            ],
        ];
    }
}
