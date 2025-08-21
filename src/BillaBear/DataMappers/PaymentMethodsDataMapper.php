<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Api\PaymentMethod as ApiDto;
use BillaBear\Dto\Generic\App\PaymentMethod as AppDto;
use BillaBear\Dto\Generic\Public\PaymentMethod as PublicDto;
use BillaBear\Repository\CustomerRepositoryInterface;
use Obol\Model\PaymentMethod\PaymentMethodCard;
use Parthenon\Billing\Entity\PaymentCard;

class PaymentMethodsDataMapper
{
    public function __construct(private CustomerRepositoryInterface $customerRepository)
    {
    }

    public function createApiDto(PaymentCard $paymentDetails): ApiDto
    {
        $dto = new ApiDto(
            $paymentDetails->getId(),
            $paymentDetails->getName(),
            $paymentDetails->isDefaultPaymentOption(),
            $paymentDetails->getBrand(),
            $paymentDetails->getLastFour(),
            $paymentDetails->getExpiryMonth(),
            $paymentDetails->getExpiryYear(),
            $paymentDetails->getCreatedAt(),
        );

        return $dto;
    }

    public function createAppDto(PaymentCard $paymentDetails): AppDto
    {
        $dto = new AppDto(
            $paymentDetails->getId(),
            $paymentDetails->getName(),
            $paymentDetails->isDefaultPaymentOption(),
            $paymentDetails->getBrand(),
            $paymentDetails->getLastFour(),
            $paymentDetails->getExpiryMonth(),
            $paymentDetails->getExpiryYear(),
            $paymentDetails->getCreatedAt(),
        );

        return $dto;
    }

    public function createFromObol(PaymentMethodCard $paymentMethodModel, ?PaymentCard $entity = null): PaymentCard
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

    public function createPublicDto(PaymentCard $paymentDetails): PublicDto
    {
        return new PublicDto(
            $paymentDetails->getId(),
            $paymentDetails->getName(),
            $paymentDetails->isDefaultPaymentOption(),
            $paymentDetails->getBrand(),
            $paymentDetails->getLastFour(),
            $paymentDetails->getExpiryMonth(),
            $paymentDetails->getExpiryYear(),
            $paymentDetails->getCreatedAt(),
        );
    }
}
