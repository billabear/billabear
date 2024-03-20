<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\PaymentAttempt as AppDto;
use App\Entity\Invoice;
use App\Entity\PaymentAttempt as Entity;
use Obol\Model\Enum\ChargeFailureReasons;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Payment;

class PaymentAttemptDataMapper
{
    public function __construct(
        private ProviderInterface $provider,
        private InvoiceDataMapper $invoiceDataMapper,
        private CustomerDataMapper $customerDataMapper,
    ) {
    }

    public function createFromInvoice(Invoice $invoice, ChargeFailureReasons $reason): Entity
    {
        $paymentAttempt = new Entity();
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
    public function createFromPayment(Payment $payment, ChargeFailureReasons $reason): Entity
    {
        $entity = new Entity();
        $entity->setCustomer($payment->getCustomer());
        $entity->setSubscriptions($payment->getSubscriptions());
        $entity->setAmount($payment->getAmount());
        $entity->setCurrency($payment->getCurrency());
        $entity->setCreatedAt(new \DateTime());
        $entity->setFailureReason($reason->value);

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setInvoice($this->invoiceDataMapper->createAppDto($entity->getInvoice()));
        $dto->setCustomer($this->customerDataMapper->createAppDto($entity->getCustomer()));
        $dto->setCurrency($entity->getCurrency());
        $dto->setAmount($entity->getAmount());
        $dto->setFailureReason($entity->getFailureReason());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }
}
