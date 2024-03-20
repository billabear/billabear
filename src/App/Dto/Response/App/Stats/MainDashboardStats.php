<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Stats;

use Symfony\Component\Serializer\Annotation\SerializedName;

class MainDashboardStats
{
    private MainDashboardHeader $header;

    #[SerializedName('subscription_count')]
    private DashboardStats $subscriptionCount;

    #[SerializedName('subscription_creation')]
    private DashboardStats $subscriptionCreation;

    #[SerializedName('subscription_cancellation')]
    private DashboardStats $subscriptionCancellation;

    #[SerializedName('payment_amount')]
    private DashboardStats $paymentAmount;

    #[SerializedName('refund_amount')]
    private DashboardStats $refundAmount;

    #[SerializedName('charge_back_amount')]
    private DashboardStats $chargeBackAmount;

    #[SerializedName('estimated_mrr')]
    private int $estimatedMrr;

    #[SerializedName('estimated_arr')]
    private int $estimatedAtt;

    private string $currency;

    public function getSubscriptionCount(): DashboardStats
    {
        return $this->subscriptionCount;
    }

    public function setSubscriptionCount(DashboardStats $subscriptionCount): void
    {
        $this->subscriptionCount = $subscriptionCount;
    }

    public function getSubscriptionCreation(): DashboardStats
    {
        return $this->subscriptionCreation;
    }

    public function setSubscriptionCreation(DashboardStats $subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }

    public function getSubscriptionCancellation(): DashboardStats
    {
        return $this->subscriptionCancellation;
    }

    public function setSubscriptionCancellation(DashboardStats $subscriptionCancellation): void
    {
        $this->subscriptionCancellation = $subscriptionCancellation;
    }

    public function getPaymentAmount(): DashboardStats
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(DashboardStats $paymentAmount): void
    {
        $this->paymentAmount = $paymentAmount;
    }

    public function getRefundAmount(): DashboardStats
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(DashboardStats $refundAmount): void
    {
        $this->refundAmount = $refundAmount;
    }

    public function getChargeBackAmount(): DashboardStats
    {
        return $this->chargeBackAmount;
    }

    public function setChargeBackAmount(DashboardStats $chargeBackAmount): void
    {
        $this->chargeBackAmount = $chargeBackAmount;
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
}
