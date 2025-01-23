<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\ExchangeRates;

use BillaBear\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class ToSystemConverter
{
    private string $currency;
    private CurrencyConverter $converter;

    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function convert(Money $originalFee): Money
    {
        if (!isset($this->currency)) {
            $this->currency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
            $this->converter = new CurrencyConverter($this->exchangeRateProvider);
        }

        return $this->converter->convert($originalFee, $this->currency, RoundingMode::HALF_DOWN);
    }
}
