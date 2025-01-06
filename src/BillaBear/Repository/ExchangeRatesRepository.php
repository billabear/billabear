<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\ExchangeRates;
use Parthenon\Common\Repository\DoctrineRepository;

class ExchangeRatesRepository extends DoctrineRepository implements ExchangeRatesRepositoryInterface
{
    public function getByCode(string $originalCurrency, string $currencyCode): ExchangeRates
    {
        $exchangeRate = $this->entityRepository->findOneBy(['originalCurrency' => $originalCurrency, 'currencyCode' => $currencyCode]);

        if (!$exchangeRate instanceof ExchangeRates) {
            $exchangeRate = new ExchangeRates();
            $exchangeRate->setOriginalCurrency($originalCurrency);
            $exchangeRate->setCurrencyCode($currencyCode);
            $exchangeRate->setExchangeRate('1');
            $exchangeRate->setUpdatedAt(new \DateTime('now'));
        }

        return $exchangeRate;
    }

    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }
}
