<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
