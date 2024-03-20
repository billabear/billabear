<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Interopt\Stripe;

use App\Dto\Interopt\Stripe\Models\SubscriptionItem as Model;
use App\Entity\Subscription;

class SubscriptionItemDataMapper
{
    public function __construct(private PriceDataMapper $priceDataMapper)
    {
    }

    public function createModel(Subscription $subscription): Model
    {
        $model = new Model();
        $model->setId((string) $subscription->getChildExternalReference());
        $model->setPrice($this->priceDataMapper->createModel($subscription->getPrice()));
        $model->setCreated($subscription->getCreatedAt()->getTimestamp());
        $model->setSubscription((string) $subscription->getId());
        $model->setQuantity(1);
        $model->setTaxRates([]);

        return $model;
    }
}
