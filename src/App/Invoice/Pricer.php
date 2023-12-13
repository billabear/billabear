<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Entity\Customer;
use App\Enum\TaxType;
use App\Tax\TaxRateProviderInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\Price;

class Pricer implements PricerInterface
{
    public function __construct(private TaxRateProviderInterface $taxRateProvider)
    {
    }

    public function getCustomerPriceInfo(Price $price, Customer $customer, TaxType $taxType, ?int $seatNumber = 1): PriceInfo
    {
        if (null === $seatNumber) {
            $seatNumber = 1;
        }

        $money = $price->getAsMoney();
        $money = $money->multipliedBy($seatNumber);
        $taxInfo = $this->taxRateProvider->getRateForCustomer($customer, $taxType, $price->getProduct());
        $rawRate = $taxInfo->rate;
        if ($price->isIncludingTax()) {
            $rate = ($rawRate / 100) + 1;
            $total = $money;
            $subTotal = $money->dividedBy($rate, RoundingMode::HALF_UP);
            $vat = $money->minus($subTotal, RoundingMode::HALF_DOWN);
        } else {
            $rate = ($rawRate / 100);
            $subTotal = $money;
            $vat = $money->multipliedBy($rate, RoundingMode::HALF_UP);
            $total = $subTotal->plus($vat, RoundingMode::HALF_DOWN);
        }

        return new PriceInfo(
            $total,
            $subTotal,
            $vat,
            $taxInfo,
        );
    }

    public function getCustomerPriceInfoFromMoney(Money $money, Customer $customer, bool $includeTax, TaxType $taxType): PriceInfo
    {
        $taxInfo = $this->taxRateProvider->getRateForCustomer($customer, $taxType);
        $rawRate = $taxInfo->rate;

        if ($includeTax) {
            $rate = ($rawRate / 100) + 1;
            $total = $money;
            $subTotal = $money->dividedBy($rate, RoundingMode::HALF_UP);
            $vat = $money->minus($subTotal, RoundingMode::HALF_DOWN);
        } else {
            $rate = ($rawRate / 100);
            $subTotal = $money;
            $vat = $money->multipliedBy($rate, RoundingMode::HALF_UP);
            $total = $subTotal->plus($vat, RoundingMode::HALF_DOWN);
        }

        return new PriceInfo(
            $total,
            $subTotal,
            $vat,
            $taxInfo,
        );
    }
}
