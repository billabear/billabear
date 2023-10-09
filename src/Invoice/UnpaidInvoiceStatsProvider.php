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

namespace App\Invoice;

use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class UnpaidInvoiceStatsProvider
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getStats()
    {
        $unpaidInvoices = $this->invoiceRepository->getOverdueInvoices();
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
