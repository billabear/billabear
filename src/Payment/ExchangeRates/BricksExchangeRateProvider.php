<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment\ExchangeRates;

use App\Repository\ExchangeRatesRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use Brick\Money\ExchangeRateProvider;

class BricksExchangeRateProvider implements ExchangeRateProvider
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository, private ExchangeRatesRepositoryInterface $exchangeRatesRepository)
    {
    }

    public function getExchangeRate(string $sourceCurrencyCode, string $targetCurrencyCode)
    {
        if ($this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency() !== $targetCurrencyCode) {
            throw new \Exception($targetCurrencyCode.' is an invalid target currency. Expected '.$this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());
        }
        $exchangeRate = $this->exchangeRatesRepository->getByCode($sourceCurrencyCode);

        return $exchangeRate->getExchangeRate();
    }
}
