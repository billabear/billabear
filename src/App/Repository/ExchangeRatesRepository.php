<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\ExchangeRates;
use Parthenon\Common\Repository\DoctrineRepository;

class ExchangeRatesRepository extends DoctrineRepository implements ExchangeRatesRepositoryInterface
{
    public function getByCode(string $currencyCode): ExchangeRates
    {
        $exchangeRate = $this->entityRepository->findOneBy(['currencyCode' => $currencyCode]);

        if (!$exchangeRate instanceof ExchangeRates) {
            $exchangeRate = new ExchangeRates();
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
