<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Stats;

use Symfony\Component\Serializer\Annotation\SerializedName;

class MainDashboardStats
{
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
}
