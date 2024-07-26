<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email\Data;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\PaymentAttempt;

class PaymentFailureEmail extends AbstractEmailData
{
    public function __construct(private PaymentAttempt $payment)
    {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_PAYMENT_FAILED;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'payment_attempt' => $this->getPaymentData($this->payment),
        ];
    }

    private function getPaymentData(PaymentAttempt $payment): array
    {
        return [
            'amount' => (string) $payment->getMoneyAmount(),
            'currency' => $payment->getCurrency(),
        ];
    }
}
