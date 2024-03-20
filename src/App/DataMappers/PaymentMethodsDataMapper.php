<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\PaymentMethod as ApiDto;
use App\Dto\Generic\App\PaymentMethod as AppDto;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Entity\PaymentCard;

class PaymentMethodsDataMapper
{
    public function __construct(private CustomerRepositoryInterface $customerRepository)
    {
    }

    public function createApiDto(PaymentCard $paymentDetails): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId($paymentDetails->getId());
        $dto->setName($paymentDetails->getName());
        $dto->setBrand($paymentDetails->getBrand());
        $dto->setExpiryMonth($paymentDetails->getExpiryMonth());
        $dto->setExpiryYear($paymentDetails->getExpiryYear());
        $dto->setLastFour($paymentDetails->getLastFour());
        $dto->setDefault($paymentDetails->isDefaultPaymentOption());

        return $dto;
    }

    public function createAppDto(PaymentCard $paymentDetails): AppDto
    {
        $dto = new AppDto();
        $dto->setId($paymentDetails->getId());
        $dto->setName($paymentDetails->getName());
        $dto->setBrand($paymentDetails->getBrand());
        $dto->setExpiryMonth($paymentDetails->getExpiryMonth());
        $dto->setExpiryYear($paymentDetails->getExpiryYear());
        $dto->setLastFour($paymentDetails->getLastFour());
        $dto->setDefault($paymentDetails->isDefaultPaymentOption());

        return $dto;
    }

    public function createFromObol(\Obol\Model\PaymentMethod\PaymentMethodCard $paymentMethodModel, ?PaymentCard $entity = null)
    {
        if (!$entity) {
            $entity = new PaymentCard();
        }
        $customer = $this->customerRepository->getByExternalReference($paymentMethodModel->getCustomerReference());
        $entity->setCustomer($customer);
        $entity->setStoredCustomerReference($paymentMethodModel->getCustomerReference());
        $entity->setStoredPaymentReference($paymentMethodModel->getId());
        $entity->setBrand($paymentMethodModel->getBrand());
        $entity->setExpiryYear($paymentMethodModel->getExpiryYear());
        $entity->setExpiryMonth($paymentMethodModel->getExpiryMonth());
        $entity->setLastFour($paymentMethodModel->getLastFour());
        $entity->setCreatedAt($paymentMethodModel->getCreatedAt());

        return $entity;
    }
}
