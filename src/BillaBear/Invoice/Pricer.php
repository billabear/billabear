<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\TaxType;
use BillaBear\Entity\TierComponent;
use BillaBear\Tax\TaxRateProviderInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Enum\PriceType;

class Pricer implements PricerInterface
{
    public function __construct(private TaxRateProviderInterface $taxRateProvider)
    {
    }

    public function getCustomerPriceInfo(
        Price $price,
        Customer $customer,
        ?TaxType $taxType,
        int|float|null $seatNumber = 1,
        int|float|null $alreadyBilled = null,
    ): array {
        if (null === $seatNumber) {
            $seatNumber = 1;
        }
        $monies = match ($price->getType()) {
            PriceType::TIERED_GRADUATED => $this->getTierGraduatedPrice($price, $seatNumber, $alreadyBilled),
            PriceType::TIERED_VOLUME => $this->getTieredVolumePrice($price, $seatNumber),
            PriceType::UNIT => [new PriceCalculation($price->getAsMoney()->multipliedBy($seatNumber, RoundingMode::HALF_CEILING), $seatNumber, $price->getAsMoney())],
            PriceType::PACKAGE => [new PriceCalculation($price->getAsMoney()->multipliedBy($seatNumber / $price->getUnits(), RoundingMode::HALF_CEILING), $seatNumber, $price->getAsMoney())],
            default => [new PriceCalculation($price->getAsMoney()->multipliedBy($seatNumber, RoundingMode::HALF_CEILING), $seatNumber, $price->getAsMoney())],
        };

        $output = [];

        /* @var PriceCalculation $money */
        foreach ($monies as $priceCalculation) {
            $money = $priceCalculation->money;
            $taxInfo = $this->taxRateProvider->getRateForCustomer($customer, $taxType, $price->getProduct(), $money);
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

            $output[] = new PriceInfo(
                $total,
                $subTotal,
                $vat,
                $taxInfo,
                floatval($priceCalculation->quantity),
                $priceCalculation->netPrice
            );
        }

        return $output;
    }

    public function getCustomerPriceInfoFromMoney(Money $money, Customer $customer, bool $includeTax, ?TaxType $taxType): PriceInfo
    {
        $taxInfo = $this->taxRateProvider->getRateForCustomer($customer, $taxType, amount: $money);
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
            floatval(1),
            $subTotal
        );
    }

    private function getTieredVolumePrice(Price $price, int $seatNumber): array
    {
        $money = Money::zero($price->getCurrency());
        /** @var TierComponent $component */
        foreach ($price->getTierComponents() as $component) {
            if ($component->getFirstUnit() <= $seatNumber && (null === $component->getLastUnit() || $component->getLastUnit() >= $seatNumber)) {
                $flatFee = Money::ofMinor($component->getFlatFee(), $price->getCurrency());

                $money = $money->plus($flatFee);

                $unitPrice = Money::ofMinor($component->getUnitPrice(), $price->getCurrency());
                $unitPriceCalculated = $unitPrice->multipliedBy($seatNumber, RoundingMode::HALF_CEILING);
                $money = $money->plus($unitPriceCalculated, RoundingMode::HALF_CEILING);

                return [new PriceCalculation($money, $seatNumber, $unitPrice)];
            }
        }

        throw new \Exception('Invalid component setup');
    }

    /**
     * @return PriceCalculation[]
     */
    private function getTierGraduatedPrice(Price $price, int $seatNumber, int|float|null $alreadyBilled): array
    {
        $output = [];
        // Handle continuous metric
        /** @var TierComponent $component */
        $seatsLeft = $seatNumber - $alreadyBilled;
        foreach ($price->getTierComponents() as $component) {
            if (null !== $alreadyBilled && null !== $component->getLastUnit() && $alreadyBilled > $component->getLastUnit()) {
                continue;
            }
            $componentMoney = Money::zero($price->getCurrency());

            if ($component->getFirstUnit() <= $seatNumber) {
                $flatFee = Money::ofMinor($component->getFlatFee(), $price->getCurrency());

                $componentMoney = $componentMoney->plus($flatFee);
                if (null !== $component->getLastUnit()) {
                    $diff = ($component->getLastUnit() - $component->getFirstUnit()) + 1;
                    if ($seatsLeft > $diff) {
                        $seatsBillable = $diff;
                    } else {
                        $seatsBillable = $seatsLeft;
                    }
                } else {
                    $seatsBillable = $seatsLeft;
                }
                $unitPrice = Money::ofMinor($component->getUnitPrice(), $price->getCurrency());
                $unitPriceCalculated = $unitPrice->multipliedBy($seatsBillable, RoundingMode::HALF_CEILING);
                $componentMoney = $componentMoney->plus($unitPriceCalculated, RoundingMode::HALF_CEILING);
                $output[] = new PriceCalculation($componentMoney, $seatsBillable, $unitPrice);
                $seatsLeft -= $seatsBillable;
            } else {
                break;
            }
        }

        return $output;
    }
}
