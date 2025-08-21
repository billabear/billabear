<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Interopt\Stripe;

use BillaBear\Dto\Interopt\Stripe\Models\SubscriptionItem as Model;
use BillaBear\Entity\Subscription;

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
