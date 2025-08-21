<?php

/*
 * Copyright Iain Cambridge 2023-2025.
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
        $customerDto = null;
        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $customerDto = $this->customerFactory->createAppDto($customer);
        }

        $dto = new AppDto(
            (string) $payment->getId(),
            $payment->getAmount(),
            $payment->getCurrency(),
            $payment->getStatus()->value,
            $payment->getPaymentReference(),
            $customerDto,
            $payment->getCreatedAt(),
        );

        return $dto;
    }

    public function createApiDto(Payment $payment): ApiDto
    {
        $receipts = $this->receiptRepository->getForPayment($payment);
        $receiptDtos = [];
        foreach ($receipts as $receipt) {
            $receiptDtos[] = $this->receiptDataMapper->createApiDto($receipt);
        }

        $customerDto = null;
        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $customerDto = $this->customerFactory->createApiDto($customer);
        }

        $dto = new ApiDto(
            (string) $payment->getId(),
            $payment->getAmount(),
            $payment->getCurrency(),
            $payment->getStatus()->value,
            $payment->getPaymentReference(),
            $customerDto,
            $payment->getCreatedAt(),
            $receiptDtos
        );

        return $dto;
    }
}
