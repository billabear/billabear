<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Usage;

use BillaBear\Entity\Usage\UsageWarning;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class UsageWarningTriggeredPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

    public function __construct(private UsageWarning $usageWarning)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::USAGE_WARNING_TRIGGERED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::USAGE_WARNING_TRIGGERED->value,
        ];
    }

    protected function buildUsageWarning(): array
    {
        return [
            'customer' => $this->getCustomerData($this->usageWarning->getCustomer()),
            'limit' => [
                'warning_level' => $this->usageWarning->getUsageLimit()->getWarningLevel()->name,
                'amount' => $this->usageWarning->getUsageLimit()->getAmount(),
            ],
            'start_of_period' => $this->usageWarning->getStartOfPeriod()->format(\DateTime::ATOM),
            'end_of_period' => $this->usageWarning->getEndOfPeriod()->format(\DateTime::ATOM),
        ];
    }
}
