<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
