<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use BillaBear\Dto\Generic\Api\Usage\Metric;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Price
{
    #[SerializedName('id')]
    private string $id;

    #[SerializedName('amount')]
    private ?int $amount;

    #[SerializedName('currency')]
    private string $currency;

    #[SerializedName('external_reference')]
    private ?string $externalReference = null;

    #[SerializedName('recurring')]
    private bool $recurring;

    #[SerializedName('schedule')]
    private ?string $schedule = null;

    #[SerializedName('including_tax')]
    private bool $includingTax = true;

    #[SerializedName('public')]
    private bool $public = true;

    #[SerializedName('usage')]
    private bool $usage = false;

    #[SerializedName('metric')]
    private ?Metric $metric;

    #[SerializedName('metric_type')]
    private ?string $metricType;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function isIncludingTax(): bool
    {
        return $this->includingTax;
    }

    public function setIncludingTax(bool $includingTax): void
    {
        $this->includingTax = $includingTax;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function isUsage(): bool
    {
        return $this->usage;
    }

    public function setUsage(bool $usage): void
    {
        $this->usage = $usage;
    }

    public function getMetric(): ?Metric
    {
        return $this->metric;
    }

    public function setMetric(?Metric $metric): void
    {
        $this->metric = $metric;
    }

    public function getMetricType(): ?string
    {
        return $this->metricType;
    }

    public function setMetricType(?string $metricType): void
    {
        $this->metricType = $metricType;
    }
}
