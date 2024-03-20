<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats;

use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class LifeTimeValueCalculation
{
    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function processStats(array $stats): array
    {
        $output = [
            'labels' => [],
            'lifespans' => [],
            'lifetime_values' => [],
            'customer_counts' => [],
        ];
        $currency = Currency::of($this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());

        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $data = [];
        foreach ($stats as $paymentTotal) {
            $key = $paymentTotal['month_date'];

            if (!isset($data[$key])) {
                $data[$key] = [
                    'total' => Money::zero($currency),
                    'customer_total' => 0,
                    'avg_durations' => [],
                ];
            }
            /** @var Money $total */
            $total = $data[$key]['total'];

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
            $total = $data[$key]['total'] = $total->plus($amountToAdd, RoundingMode::HALF_UP);
            $data[$key]['customer_total'] += $paymentTotal['customer_count'];
            $data[$key]['avg_durations'][] = $paymentTotal['avg_duration'];
        }

        foreach ($data as $date => $row) {
            /** @var Money $total */
            $total = $row['total'];

            $lifespan = (array_sum($row['avg_durations']) / count($row['avg_durations'])) / 60 / 60 / 24 / 365;
            $customerCount = $row['customer_total'];

            if (0 !== $customerCount && 0 !== $lifespan) {
                $lifeTime = $total->getAmount()->dividedBy($customerCount, roundingMode: RoundingMode::HALF_UP)->dividedBy($lifespan, roundingMode: RoundingMode::HALF_UP);
            } else {
                $lifeTime = Money::zero($currency)->getAmount();
            }

            $dateTime = new \DateTime($date);

            $output['labels'][] = $dateTime->format('Y-m-d');
            $output['lifespans'][] = $lifespan;
            $output['customer_counts'][] = $customerCount;
            $output['lifetime_values'][] = (string) $lifeTime;
        }

        return $output;
    }
}
