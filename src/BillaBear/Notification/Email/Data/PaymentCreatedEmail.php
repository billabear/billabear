<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email\Data;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Receipt;

class PaymentCreatedEmail extends AbstractEmailData
{
    public function __construct(private Payment $payment, private Receipt $receipt)
    {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_PAYMENT_SUCCEEDED;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'payment' => $this->getPaymentData($this->payment),
            'receipt' => $this->getReceiptData($this->receipt),
        ];
    }

    private function getPaymentData(Payment $payment): array
    {
        return [
            'amount' => (string) $payment->getMoneyAmount(),
            'currency' => $payment->getCurrency(),
            'description' => $payment->getDescription(),
        ];
    }

    private function getReceiptData(Receipt $receipt)
    {
        return [
            'total' => (string) $receipt->getTotalMoney(),
            'tax_total' => (string) $receipt->getVatTotalMoney(),
            'sub_total' => (string) $receipt->getSubTotalMoney(),
        ];
    }
}
