<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Stats;

use App\DataMappers\Settings\BrandSettingsDataMapper;
use App\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use App\Dto\Response\App\Stats\LifetimeValue as LifetimeValueDto;
use App\Filters\Stats\LifetimeValue;
use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\LifetimeValueStatsRepositoryInterface;
use App\Repository\SubscriptionPlanRepositoryInterface;
use App\Stats\LifeTimeValueCalculation;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FinancialController
{
    #[Route('/app/stats/lifetime', name: 'app_app_stats_financial_lifetimevalue', methods: ['GET'])]
    public function lifetimeValue(
        Request $request,
        LifetimeValueStatsRepositoryInterface $lifetimeValueStatsRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
        BrandSettingsDataMapper $brandSettingsDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        BricksExchangeRateProvider $exchangeRateProvider,
        LifeTimeValueCalculation $calculation,
        SerializerInterface $serializer,
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

        $rawData = $lifetimeValueStatsRepository->getLifetimeValue($filters);
        $graphData = $calculation->processStats($rawData);

        $plans = $subscriptionPlanRepository->getAll();
        $planDtos = array_map([$subscriptionPlanDataMapper, 'createAppDto'], $plans);

        $brands = $brandSettingsRepository->getAll();
        $brandDtos = array_map([$brandSettingsDataMapper, 'createAppDto'], $brands);

        $dto = new LifetimeValueDto();
        $dto->setLifetimeValue($lifeTime->toInt());
        $dto->setLifespan($lifespan);
        $dto->setCurrency($currency->getCurrencyCode());
        $dto->setCustomerCount($customerCount);
        $dto->setBrands($brandDtos);
        $dto->setPlans($planDtos);
        $dto->setGraphData($graphData);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
