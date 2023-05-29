<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory;

use App\Entity\Invoice;
use App\Entity\PaymentAttempt;
use Obol\Model\Enum\ChargeFailureReasons;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Payment;

class PaymentAttemptFactory
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    public function createFromInvoice(Invoice $invoice, ChargeFailureReasons $reason): PaymentAttempt
    {
        $paymentAttempt = new PaymentAttempt();
        $paymentAttempt->setInvoice($invoice);
        $paymentAttempt->setCustomer($invoice->getCustomer());
        $paymentAttempt->setSubscriptions($invoice->getSubscriptions());
        $paymentAttempt->setAmount($invoice->getAmountDue());
        $paymentAttempt->setCurrency($invoice->getCurrency());
        $paymentAttempt->setCreatedAt(new \DateTime());
        $paymentAttempt->setFailureReason($reason->value);

        return $paymentAttempt;
    }

    // TODO remove the need for this
    public function createFromPayment(Payment $payment, ChargeFailureReasons $reason): PaymentAttempt
    {
        $entity = new PaymentAttempt();
        $entity->setCustomer($payment->getCustomer());
        $entity->setSubscriptions($payment->getSubscriptions());
        $entity->setAmount($payment->getAmount());
        $entity->setCurrency($payment->getCurrency());
        $entity->setCreatedAt(new \DateTime());
        $entity->setFailureReason($reason->value);

        return $entity;
    }
}
