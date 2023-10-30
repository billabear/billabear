<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Stats;

use App\Api\Filters\Stats\LifetimeValue;
use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\LifetimeValueStatsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FinancialController
{
    #[Route('/app/stats/lifetime', name: 'app_app_stats_financial_lifetimevalue', methods: ['GET'])]
    public function lifetimeValue(
        Request $request,
        LifetimeValueStatsRepositoryInterface $lifetimeValueStatsRepository,
        SettingsRepositoryInterface $settingsRepository,
        BricksExchangeRateProvider $exchangeRateProvider,
    ) {
        $currency = Currency::of($settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());

        $currencyConverter = new CurrencyConverter($exchangeRateProvider);
        $filtersBuilder = new LifetimeValue();
        $filters = $filtersBuilder->getFilters($request);

        $lifespan = $lifetimeValueStatsRepository->getAverageLifespan($filters);
        $customerCount = $lifetimeValueStatsRepository->getUniqueCustomerCount($filters);
        $paymentTotals = $lifetimeValueStatsRepository->getPaymentTotals($filters);
        $total = Money::zero($currency);
        foreach ($paymentTotals as $paymentTotal) {
            $originalFee = Money::ofMinor($paymentTotal['amount'], $paymentTotal['currency']);
            if ($paymentTotal['currency'] !== $currency->getCurrencyCode()) {
                $amountToAdd = $currencyConverter->convert($originalFee, $currency, RoundingMode::HALF_DOWN);
            } else {
                $amountToAdd = $originalFee;
            }

            $modifier = match ($paymentTotal['payment_schedule']) {
                'week' => 52,
                'month' => 12,
                default => 1,
            };
            $amountToAdd = $amountToAdd->multipliedBy($modifier, RoundingMode::HALF_UP);
            $total = $total->plus($amountToAdd, RoundingMode::HALF_UP);
        }

        if (0 !== $customerCount && 0 !== $lifespan) {
            $lifeTime = $total->getMinorAmount()->dividedBy($customerCount, roundingMode: RoundingMode::HALF_UP)->dividedBy($lifespan, roundingMode: RoundingMode::HALF_UP);
        } else {
            $lifeTime = Money::zero($currency)->getMinorAmount();
        }

        return new JsonResponse([
            'lifetime_value' => $lifeTime->toInt(),
            'lifespan' => $lifespan,
            'currency' => $currency->getCurrencyCode(),
        ]);
    }
}
