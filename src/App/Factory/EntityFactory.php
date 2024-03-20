<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Factory;

use App\Entity\Payment;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Receipt;
use App\Entity\ReceiptLine;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Entity\PriceInterface;
use Parthenon\Billing\Entity\ProductInterface;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\ReceiptLineInterface;
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

    public function getReceipt(): ReceiptInterface
    {
        return new Receipt();
    }

    public function getReceiptLine(): ReceiptLineInterface
    {
        return new ReceiptLine();
    }
}
