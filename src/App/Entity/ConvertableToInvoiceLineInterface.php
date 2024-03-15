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

namespace App\Entity;

use Brick\Money\Money;

interface ConvertableToInvoiceLineInterface
{
    public function getId();

    public function getCurrency(): string;

    public function getTotal(): int;

    public function getSubTotal(): int;

    public function getTaxTotal(): int;

    public function getDescription(): ?string;

    public function getTotalMoney(): Money;

    public function getVatTotalMoney(): Money;

    public function getSubTotalMoney(): Money;

    public function getTaxPercentage(): ?float;

    public function getTaxType(): TaxType;

    public function getSubscriptionPlan(): ?SubscriptionPlan;

    public function getPrice(): ?Price;

    public function isIncludeTax(): bool;

    public function getTaxCountry(): ?string;

    public function isReverseCharge(): bool;

    public function getSeatNumber(): ?int;
}
