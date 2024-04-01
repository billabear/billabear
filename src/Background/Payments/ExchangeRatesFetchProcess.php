<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Payments;

use App\Payment\ExchangeRates\ProviderInterface;
use App\Repository\ExchangeRatesRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;

class ExchangeRatesFetchProcess
{
    public function __construct(
        private ProviderInterface $provider,
        private SettingsRepositoryInterface $settingsRepository,
        private ExchangeRatesRepositoryInterface $repository,
    ) {
    }

    public function process(): void
    {
        $rates = $this->provider->getRates($this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());

        foreach ($rates as $currencyCode => $rate) {
            $exchangeRate = $this->repository->getByCode($currencyCode);
            $exchangeRate->setExchangeRate((string) $rate);
            $exchangeRate->setUpdatedAt(new \DateTime('now'));
            $this->repository->save($exchangeRate);
        }
    }
}
