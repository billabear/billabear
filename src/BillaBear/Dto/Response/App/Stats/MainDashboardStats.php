<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Stats;

use Symfony\Component\Serializer\Annotation\SerializedName;

class MainDashboardStats
{
    private MainDashboardHeader $header;

    #[SerializedName('subscription_count')]
    private DashboardStats $subscriptionCount;

    #[SerializedName('revenue_stats')]
    private DashboardStats $revenueStats;

    #[SerializedName('estimated_mrr')]
    private int $estimatedMrr;

    #[SerializedName('estimated_arr')]
    private int $estimatedAtt;

    private string $currency;

    #[SerializedName('latest_customers')]
    private array $latestCustomers;

    #[SerializedName('subscription_events')]
    private array $subscriptionEvents;

    #[SerializedName('latest_payments')]
    private array $latestPayments;

    public function getSubscriptionCount(): DashboardStats
    {
        return $this->subscriptionCount;
    }

    public function setSubscriptionCount(DashboardStats $subscriptionCount): void
    {
        $this->subscriptionCount = $subscriptionCount;
    }

    public function getRevenueStats(): DashboardStats
    {
        return $this->revenueStats;
    }

    public function setRevenueStats(DashboardStats $revenueStats): void
    {
        $this->revenueStats = $revenueStats;
    }

    public function getEstimatedMrr(): int
    {
        return $this->estimatedMrr;
    }

    public function setEstimatedMrr(int $estimatedMrr): void
    {
        $this->estimatedMrr = $estimatedMrr;
    }

    public function getEstimatedAtt(): int
    {
        return $this->estimatedAtt;
    }

    public function setEstimatedAtt(int $estimatedAtt): void
    {
        $this->estimatedAtt = $estimatedAtt;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getHeader(): MainDashboardHeader
    {
        return $this->header;
    }

    public function setHeader(MainDashboardHeader $header): void
    {
        $this->header = $header;
    }

    public function getLatestCustomers(): array
    {
        return $this->latestCustomers;
    }

    public function setLatestCustomers(array $latestCustomers): void
    {
        $this->latestCustomers = $latestCustomers;
    }

    public function getSubscriptionEvents(): array
    {
        return $this->subscriptionEvents;
    }

    public function setSubscriptionEvents(array $subscriptionEvents): void
    {
        $this->subscriptionEvents = $subscriptionEvents;
    }

    public function getLatestPayments(): array
    {
        return $this->latestPayments;
    }

    public function setLatestPayments(array $latestPayments): void
    {
        $this->latestPayments = $latestPayments;
    }
}
