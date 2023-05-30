<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
