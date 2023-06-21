<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
