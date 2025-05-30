<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\ChargeBack as AppDto;
use BillaBear\Entity\Customer;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Enum\ChargeBackReason;
use Parthenon\Billing\Enum\ChargeBackStatus;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class ChargeBackDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory,
        private PaymentDataMapper $paymentFactory,
        private PaymentRepositoryInterface $paymentRepository,
    ) {
    }

    public function createAppDto(ChargeBack $chargeBack): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $chargeBack->getId());
        $dto->setPayment($this->paymentFactory->createAppDto($chargeBack->getPayment()));
        $customer = $chargeBack->getCustomer();
        if ($customer instanceof Customer) {
            $dto->setCustomer($this->customerFactory->createAppDto($customer));
        }
        $dto->setStatus($chargeBack->getStatus()->value);
        $dto->setReason($chargeBack->getReason()->value);
        $dto->setCreatedAt($chargeBack->getCreatedAt());

        return $dto;
    }

    public function createEntity(\Obol\Model\ChargeBack\ChargeBack $model, ?ChargeBack $chargeBack): ChargeBack
    {
        if (!$chargeBack) {
            $chargeBack = new ChargeBack();
        }

        $payment = $this->paymentRepository->getPaymentForReference($model->getPaymentReference());

        $chargeBack->setPayment($payment);
        $customer = $payment->getCustomer();
        if ($customer) {
            $chargeBack->setCustomer($customer);
        }
        $chargeBack->setExternalReference($model->getId());
        $chargeBack->setStatus(ChargeBackStatus::fromName($model->getStatus()));
        $chargeBack->setReason(ChargeBackReason::fromName($model->getReason()));
        $chargeBack->setCreatedAt($model->getCreatedAt() ?? new \DateTime('now'));
        $chargeBack->setUpdatedAt(new \DateTime('now'));

        return $chargeBack;
    }
}
