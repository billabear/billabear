<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

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

    public function getTaxType(): ?TaxType;

    public function getSubscriptionPlan(): ?SubscriptionPlan;

    public function getPrice(): ?Price;

    public function isIncludeTax(): bool;

    public function getTaxCountry(): ?string;

    public function getTaxState(): ?string;

    public function isReverseCharge(): bool;

    public function getSeatNumber(): ?int;
}
