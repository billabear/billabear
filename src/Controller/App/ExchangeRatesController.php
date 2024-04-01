<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Repository\ExchangeRatesRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRatesController
{
    #[Route('/app/exchange-rates', name: 'app_app_exchangerates_getexchangerates')]
    public function getExchangeRates(
        ExchangeRatesRepositoryInterface $exchangeRatesRepository
    ) {
        $output = [];
        $rates = $exchangeRatesRepository->getAll();
        foreach ($rates as $rate) {
            $output[] = [
                'currency_code' => $rate->getCurrencyCode(),
                'rate' => $rate->getExchangeRate(),
            ];
        }

        return new JsonResponse(['rates' => $output]);
    }
}
