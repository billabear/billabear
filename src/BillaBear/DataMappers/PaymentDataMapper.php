<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Api\Payment as ApiDto;
use BillaBear\Dto\Generic\App\Payment as AppDto;
use BillaBear\Entity\Customer;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;

class PaymentDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory,
        private ReceiptDataMapper $receiptDataMapper,
        private ReceiptRepositoryInterface $receiptRepository,
    ) {
    }

    public function createAppDto(Payment $payment): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $payment->getId());
        $dto->setStatus($payment->getStatus()->value);
        $dto->setAmount($payment->getAmount());
        $dto->setCurrency($payment->getCurrency());
        $dto->setExternalReference($payment->getPaymentReference());
        $dto->setCreatedAt($payment->getCreatedAt());
        $dto->setPaymentProviderDetailsUrl($payment->getPaymentProviderDetailsUrl());

        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $dto->setCustomer($this->customerFactory->createAppDto($customer));
        }

        return $dto;
    }

    public function createApiDto(Payment $payment): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $payment->getId());
        $dto->setStatus($payment->getStatus()->value);
        $dto->setAmount($payment->getAmount());
        $dto->setCurrency($payment->getCurrency());
        $dto->setExternalReference($payment->getPaymentReference());
        $dto->setCreatedAt($payment->getCreatedAt());

        $receipts = $this->receiptRepository->getForPayment($payment);
        $receiptDtos = [];
        foreach ($receipts as $receipt) {
            $receiptDtos[] = $this->receiptDataMapper->createApiDto($receipt);
        }
        $dto->setReceipts($receiptDtos);

        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $dto->setCustomer($this->customerFactory->createApiDto($customer));
        }

        return $dto;
    }
}
