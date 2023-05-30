<?php

namespace App\Stats;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\ExchangeRatesRepository;
use App\Repository\ExchangeRatesRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\MonthlyRevenueStatsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\CurrencyConverter;
use Parthenon\Billing\Entity\Subscription;

class MonthlyRevenueStats
{
    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
        private MonthlyRevenueStatsRepositoryInterface $monthlyRevenueStatsRepository,
    )
    {

    }

    public function addSubscription(Invoice $invoice): void
    {
        $defaultCurrency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $amountToAdd = $currencyConverter->convert($invoice->getAmountDueAsMoney(), , RoundingMode::HALF_DOWN);

        $customer = $invoice->getCustomer();
        $brand = $customer?->getBrand() ?? Customer::DEFAULT_BRAND;

        $monthlyStat = $this->monthlyRevenueStatsRepository->getFromToStats($invoice->getCreatedAt(), $defaultCurrency, $brand);
        $monthlyStat->increaseAmount($amountToAdd);
        $this->dailyStatsRepository->save($monthlyStat);

    }
}