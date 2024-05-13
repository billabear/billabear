<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Payments;

use BillaBear\Payment\ExchangeRates\ProviderInterface;
use BillaBear\Repository\ExchangeRatesRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;

class ExchangeRatesFetchProcess
{
    public function __construct(
        private ProviderInterface $provider,
        private SettingsRepositoryInterface $settingsRepository,
        private ExchangeRatesRepositoryInterface $repository,
        private PriceRepositoryInterface $priceRepository,
    ) {
    }

    public function process(): void
    {
        $prices = $this->priceRepository->getAll();
        $currencies = [];

        foreach ($prices as $price) {
            if (!isset($currencies[$price->getCurrency()])) {
                $currencies[] = $price->getCurrency();
            }
        }
        foreach ($currencies as $originalCurrency) {
            $rates = $this->provider->getRates($originalCurrency);

            foreach ($rates as $currencyCode => $rate) {
                $exchangeRate = $this->repository->getByCode($originalCurrency, $currencyCode);
                $exchangeRate->setExchangeRate((string) $rate);
                $exchangeRate->setUpdatedAt(new \DateTime('now'));
                $this->repository->save($exchangeRate);
            }
        }
    }
}
