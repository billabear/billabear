<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Voucher;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Subscription;

class ExpiringCardBeforeChargeNotValid extends AbstractEmailData
{
    use VoucherTrait;

    public function __construct(private PaymentCard $paymentCard, private Subscription $subscription, private ?Voucher $voucher = null)
    {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_NOT_VALID_WARNING;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'payment_card' => $this->getPaymentCardData($this->paymentCard),
            'subscription' => $this->getSubscriptionData($this->subscription),
            'voucher' => $this->getVoucherData($this->voucher, $this->subscription),
        ];
    }

    protected function getSubscriptionData(Subscription $subscription): array
    {
        return [
            'plan_name' => $subscription->getPlanName(),
            'has_trial' => $subscription->isHasTrial(),
            'trial_length' => $subscription->getTrialLengthDays(),
            'payment_schedule' => $subscription->getPaymentSchedule(),
            'amount' => (string) $subscription->getMoneyAmount(),
            'next_payment_due' => $subscription->getValidUntil()->format(\DATE_ATOM),
        ];
    }

    private function getPaymentCardData(PaymentCard $paymentCard): array
    {
        return [
            'last_four' => $paymentCard->getLastFour(),
            'expiry_month' => $paymentCard->getExpiryMonth(),
            'expiry_year' => $paymentCard->getExpiryYear(),
        ];
    }
}
