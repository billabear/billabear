<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory;

use App\Entity\Payment;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Entity\PriceInterface;
use Parthenon\Billing\Entity\ProductInterface;
use Parthenon\Billing\Entity\SubscriptionPlanInterface;
use Parthenon\Billing\Factory\EntityFactoryInterface;

class EntityFactory implements EntityFactoryInterface
{
    public function getProductEntity(): ProductInterface
    {
        return new Product();
    }

    public function getPriceEntity(): PriceInterface
    {
        return new Price();
    }

    public function getSubscriptionPlanEntity(): SubscriptionPlanInterface
    {
        return new SubscriptionPlan();
    }

    public function getSubscriptionEntity(): Subscription
    {
        return new Subscription();
    }

    public function getPaymentEntity(): Payment
    {
        return new Payment();
    }

    public function getChargeBackEntity(): ChargeBack
    {
        return new ChargeBack();
    }
}
