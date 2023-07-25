<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
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
