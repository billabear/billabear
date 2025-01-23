<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\ExchangeRates;

use BillaBear\Repository\ExchangeRatesRepositoryInterface;
use Brick\Money\ExchangeRateProvider;

class BricksExchangeRateProvider implements ExchangeRateProvider
{
    public function __construct(private ExchangeRatesRepositoryInterface $exchangeRatesRepository)
    {
    }

    public function getExchangeRate(string $sourceCurrencyCode, string $targetCurrencyCode)
    {
        $exchangeRate = $this->exchangeRatesRepository->getByCode($sourceCurrencyCode, $targetCurrencyCode);

        return $exchangeRate->getExchangeRate();
    }
}
