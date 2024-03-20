<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Filters\Interopt\Stripe;

use App\Entity\Customer;
use App\Filters\AbstractFilterList;
use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Parthenon\Athena\Filters\GreaterThanFilter;
use Parthenon\Athena\Filters\GreaterThanOrEqualFilter;
use Parthenon\Athena\Filters\LessThanFilter;
use Parthenon\Athena\Filters\LessThanOrEqualFilter;
use Parthenon\Billing\Enum\SubscriptionStatus;

class SubscriptionList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        $timestampConverter = function ($value) {
            if (!ctype_digit($value)) {
                throw new \Exception('Invalid data type');
            }

            $dateTime = new \DateTime();
            $dateTime->setTimestamp($value);

            return $dateTime;
        };

        return [
            'customer' => [
                'field' => 'customer',
                'filter' => ExactChoiceFilter::class,
            ],
            'price' => [
                'field' => 'price',
                'filter' => ExactChoiceFilter::class,
            ],
            'created' => [
                'field' => 'createdAt',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[gt]' => [
                'field' => 'createdAt',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[gte]' => [
                'field' => 'createdAt',
                'filter' => GreaterThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[lt]' => [
                'field' => 'createdAt',
                'filter' => LessThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[lte]' => [
                'field' => 'createdAt',
                'filter' => LessThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_end' => [
                'field' => 'validUntil',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_end[gt]' => [
                'field' => 'validUntil',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_end[gte]' => [
                'field' => 'validUntil',
                'filter' => GreaterThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_end[lt]' => [
                'field' => 'validUntil',
                'filter' => LessThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_end[lte]' => [
                'field' => 'validUntil',
                'filter' => LessThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_start' => [
                'field' => 'startOfCurrentPeriod',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_start[gt]' => [
                'field' => 'startOfCurrentPeriod',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_start[gte]' => [
                'field' => 'startOfCurrentPeriod',
                'filter' => GreaterThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_start[lt]' => [
                'field' => 'startOfCurrentPeriod',
                'filter' => LessThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'current_period_start[lte]' => [
                'field' => 'startOfCurrentPeriod',
                'filter' => LessThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'collection_method' => [
                'field' => 'customer.billingType',
                'filter' => ExactChoiceFilter::class,
                'converter' => function ($value) {
                    if ('charge_automatically' === $value) {
                        return Customer::BILLING_TYPE_CARD;
                    } elseif ('send_invoice' === $value) {
                        return Customer::BILLING_TYPE_INVOICE;
                    } else {
                        throw new \Exception('Invalid collection method');
                    }
                },
            ],
            'status' => [
                'field' => 'status',
                'filter' => ContainsFilter::class,
                'converter' => function ($value) {
                    return match ($value) {
                        'active','trialing' => SubscriptionStatus::ACTIVE->value,
                        'past_due','unpaid' => SubscriptionStatus::OVERDUE_PAYMENT_OPEN->value,
                        'paused' => SubscriptionStatus::PAUSED->value,
                        'ended', 'canceled' => SubscriptionStatus::CANCELLED->value,
                        'all' => '%',
                        default => throw new \Exception('Invalid status'),
                    };
                },
            ],
        ];
    }
}
