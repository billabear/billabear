<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Interopt\Stripe;

use App\Dto\Interopt\Stripe\Models\Price as Model;
use App\Entity\Price as Entity;

class PriceDataMapper
{
    public function createModel(Entity $price): Model
    {
        $model = new Model();
        $model->setId((string) $price->getId());
        $model->setProduct((string) $price->getProduct()->getId());
        $model->setCurrency($price->getCurrency());
        $model->setUnitAmount($price->getAmount());
        $model->setUnitAmountDecimal((string) $price->getAmount());
        $model->setCreated($price->getCreatedAt()->getTimestamp());
        $model->setLivemode(true);
        $model->setBillingScheme('per_unit');
        $model->setTaxBehavior($price->isIncludingTax() ? 'inclusive' : 'exclusive');
        $model->setType('one-off' === $price->getSchedule() ? 'one_time' : 'recurring');

        if ('one-off' !== $price->getSchedule()) {
            $recurringData = [
                'aggregate_usage' => null,
                'interval' => $price->getSchedule(),
                'interval_count' => 1,
                'usage_type' => 'licensed',
            ];

            $model->setRecurring($recurringData);
        }

        return $model;
    }
}
