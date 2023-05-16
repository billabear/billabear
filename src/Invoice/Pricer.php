<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Entity\Customer;
use App\Tax\TaxRateProviderInterface;
use Brick\Math\RoundingMode;
use Parthenon\Billing\Entity\Price;

class Pricer implements PricerInterface
{
    public function __construct(private TaxRateProviderInterface $taxRateProvider)
    {
    }

    public function getCustomerPriceInfo(Price $price, Customer $customer): PriceInfo
    {
        $money = $price->getAsMoney();
        $rawRate = $this->taxRateProvider->getRateForCustomer($customer);

        $rate = ($rawRate / 100) + 1;

        $subTotal = $money->dividedBy($rate, RoundingMode::HALF_UP);
        $vat = $money->minus($subTotal, RoundingMode::HALF_DOWN);

        return new PriceInfo(
            $money,
            $subTotal,
            $vat,
            $rawRate,
        );
    }
}
