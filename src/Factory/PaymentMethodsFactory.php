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

namespace App\Factory;

use App\Dto\Generic\Api\PaymentMethod as ApiDto;
use App\Dto\Generic\App\PaymentMethod as AppDto;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Entity\PaymentCard;

class PaymentMethodsFactory
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

    public function createFromObol(\Obol\Model\PaymentMethod\PaymentMethodCard $paymentMethodModel, PaymentCard $entity = null)
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
