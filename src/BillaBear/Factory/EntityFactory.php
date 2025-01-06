<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Factory;

use BillaBear\Entity\Payment;
use BillaBear\Entity\Price;
use BillaBear\Entity\Product;
use BillaBear\Entity\Receipt;
use BillaBear\Entity\ReceiptLine;
use BillaBear\Entity\Refund;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
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

    public function getRefundEntity(): Refund
    {
        return new Refund();
    }
}
