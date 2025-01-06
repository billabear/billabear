<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Tax;

use BillaBear\Dto\Generic\App\Country;
use Symfony\Component\Serializer\Attribute\SerializedName;

class ActiveTaxCountries
{
    private Country $country;

    #[SerializedName('threshold_amount')]
    private int $thresholdAmount;

    #[SerializedName('transacted_amount')]
    private int $transactedAmount;

    #[SerializedName('threshold_reached')]
    private bool $thresholdReached;

    #[SerializedName('collected_amount')]
    private int $collectedAmount;

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getThresholdAmount(): int
    {
        return $this->thresholdAmount;
    }

    public function setThresholdAmount(int $thresholdAmount): void
    {
        $this->thresholdAmount = $thresholdAmount;
    }

    public function getTransactedAmount(): int
    {
        return $this->transactedAmount;
    }

    public function setTransactedAmount(int $transactedAmount): void
    {
        $this->transactedAmount = $transactedAmount;
    }

    public function isThresholdReached(): bool
    {
        return $this->thresholdReached;
    }

    public function setThresholdReached(bool $thresholdReached): void
    {
        $this->thresholdReached = $thresholdReached;
    }

    public function getCollectedAmount(): int
    {
        return $this->collectedAmount;
    }

    public function setCollectedAmount(int $collectedAmount): void
    {
        $this->collectedAmount = $collectedAmount;
    }
}
