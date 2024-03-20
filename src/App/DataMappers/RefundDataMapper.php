<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\Refund as ApiDto;
use App\Dto\Generic\App\Refund as AppDto;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Enum\RefundStatus;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class RefundDataMapper
{
    public function __construct(
        private PaymentDataMapper $paymentFactory,
        private CustomerDataMapper $customerFactory,
        private BillingAdminDataMapper $billingAdminFactory,
        public PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function createAppDto(Refund $refund): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $refund->getId());
        $dto->setAmount($refund->getAmount());
        $dto->setCurrency($refund->getCurrency());
        $dto->setReason($refund->getReason());
        $dto->setStatus($refund->getStatus()->value);
        $dto->setCreatedAt($refund->getCreatedAt());
        $dto->setPayment($this->paymentFactory->createAppDto($refund->getPayment()));
        $dto->setCustomer($this->customerFactory->createAppDto($refund->getCustomer()));
        $dto->setBillingAdmin($this->billingAdminFactory->createAppDto($refund->getBillingAdmin()));

        return $dto;
    }

    public function createApiDto(Refund $refund): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $refund->getId());
        $dto->setAmount($refund->getAmount());
        $dto->setCurrency($refund->getCurrency());
        $dto->setComment($refund->getReason());
        $dto->setStatus($refund->getStatus()->value);
        $dto->setCreatedAt($refund->getCreatedAt());
        $dto->setPayment($this->paymentFactory->createApiDto($refund->getPayment()));
        $dto->setCustomer($this->customerFactory->createApiDto($refund->getCustomer()));
        $dto->setBillingAdmin($this->billingAdminFactory->createAppDto($refund->getBillingAdmin()));

        return $dto;
    }

    public function createEntity(\Obol\Model\Refund $model, ?Refund $refund = null): Refund
    {
        if (!$refund) {
            $refund = new Refund();
        }
        $payment = $this->paymentRepository->getPaymentForReference($model->getPaymentId());

        $refund->setAmount($model->getAmount());
        $refund->setCurrency(strtoupper($model->getCurrency()));
        $customer = $payment->getCustomer();
        if ($customer) {
            $refund->setCustomer($customer);
        }
        $refund->setPayment($payment);
        $refund->setCreatedAt($model->getCreatedAt() ?? new \DateTime());
        $refund->setExternalReference($model->getId());
        $refund->setStatus(RefundStatus::ISSUED);
        $refund->setReason('imported');

        $payment->setStatus($payment->getAmount() === $refund->getAmount() ? PaymentStatus::FULLY_REFUNDED : PaymentStatus::PARTIALLY_REFUNDED);

        return $refund;
    }
}
