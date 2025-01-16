<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow;

enum WorkflowType: string
{
    case CANCEL_SUBSCRIPTION = 'cancel_subscription';
    case CREATE_SUBSCRIPTION = 'create_subscription';
    case TRIAL_STARTED = 'trial_started';
    case TRIAL_ENDED = 'trial_ended';
    case TRIAL_CONVERTED = 'trial_extended';
    case CREATE_PAYMENT = 'create_payment';
    case CREATE_CHARGEBACK = 'create_chargeback';
    case CREATE_REFUND = 'create_refund';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromName(?string $name): self
    {
        if (!$name) {
            return self::CANCEL_SUBSCRIPTION;
        }
        foreach (self::cases() as $status) {
            if ($name === $status->value) {
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }
}
