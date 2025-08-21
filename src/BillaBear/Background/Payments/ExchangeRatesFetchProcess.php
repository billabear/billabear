<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Payments;

use BillaBear\Payment\ExchangeRates\ProviderInterface;
use BillaBear\Repository\ExchangeRatesRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;

readonly class ExchangeRatesFetchProcess
{
    public function __construct(
        private ProviderInterface $provider,
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
