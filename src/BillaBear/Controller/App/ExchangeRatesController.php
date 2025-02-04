<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Repository\ExchangeRatesRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ExchangeRatesController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/exchange-rates', name: 'app_app_exchangerates_getexchangerates')]
    public function getExchangeRates(
        ExchangeRatesRepositoryInterface $exchangeRatesRepository,
    ) {
        $this->getLogger()->info('Received request to see exchange rates');

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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
