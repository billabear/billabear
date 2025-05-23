<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Parthenon\Common\LoggerAwareTrait;

class UnpaidInvoiceStatsProvider
{
    use LoggerAwareTrait;

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getStats()
    {
        $unpaidInvoices = $this->invoiceRepository->getUnpaidInvoices();
        $defaultCurrency = strtoupper($this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());

        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $money = Money::zero($defaultCurrency);
        $count = 0;
        foreach ($unpaidInvoices as $unpaidInvoice) {
            $amountDue = $unpaidInvoice->getAmountDueAsMoney();
            $amountToAdd = $currencyConverter->convert($amountDue, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
            ++$count;
        }

        return [$count, $money->getMinorAmount()->toInt()];
    }
}
