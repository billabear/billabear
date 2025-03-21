<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Subscriptions;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\Dto\Generic\Api\Subscription as ApiDto;
use BillaBear\Dto\Generic\App\Subscription as AppDto;
use BillaBear\Entity\Subscription as Entity;
use BillaBear\Repository\CustomerRepositoryInterface;
use Obol\Model\Subscription as ObolModel;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class SubscriptionDataMapper
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionPlanDataMapper $subscriptionPlanFactory,
        private PriceDataMapper $priceFactory,
        private CustomerDataMapper $customerFactory,
        private CustomerRepositoryInterface $customerRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PaymentCardRepositoryInterface $paymentCardRepository,
    ) {
    }

    public function createFromObol(ObolModel $model, ?Entity $subscription = null): Entity
    {
        if (!$subscription) {
            $subscription = new Entity();
        }
        $subscription->setMainExternalReference($model->getId());
        $subscription->setChildExternalReference($model->getLineId());
        $subscription->setCreatedAt($model->getCreatedAt());
        $subscription->setUpdatedAt(new \DateTime());
        $subscription->setStartOfCurrentPeriod($model->getStartOfCurrentPeriod());
        $subscription->setValidUntil($model->getValidUntil());
        $subscription->setHasTrial($model->hasTrial());

        $subscription->setMoneyAmount($model->getCostPerSeat());
        $status = match ($model->getStatus()) {
            'active' => SubscriptionStatus::ACTIVE,
            'overdue' => SubscriptionStatus::OVERDUE_PAYMENT_OPEN,
            default => SubscriptionStatus::CANCELLED,
        };

        $subscription->setEndedAt($model->getCancelledAt());
        $subscription->setStatus($status);

        $customer = $this->customerRepository->getByExternalReference($model->getCustomerReference());
        $subscription->setCustomer($customer);

        $price = $this->priceRepository->getByExternalReference($model->getPriceId());
        $subscriptionPlans = $this->subscriptionPlanRepository->getAllForProduct($price->getProduct());
        if (!isset($subscriptionPlans[0])) {
            throw new \Exception('No subscription plan');
        }
        $subscription->setPrice($price);
        $subscription->setSubscriptionPlan($subscriptionPlans[0]);
        $subscription->setPlanName($price->getProduct()->getName());
        $subscription->setPaymentSchedule($price->getSchedule());

        if ($model->getStoredPaymentReference()) {
            try {
                $paymentMethod = $this->paymentCardRepository->getPaymentCardForReference($model->getStoredPaymentReference());
                $subscription->setPaymentDetails($paymentMethod);
            } catch (NoEntityFoundException $e) {
                $this->getLogger()->warning('There is no payment method found', ['payment_method_reference' => $model->getStoredPaymentReference()]);
            }
        }

        return $subscription;
    }

    public function createAppDto(Entity $subscription): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $subscription->getId());
        $dto->setStatus($subscription->getStatus()->value);
        $dto->setSchedule($subscription->getPaymentSchedule());
        if ($subscription->getSubscriptionPlan()) {
            $dto->setSubscriptionPlan($this->subscriptionPlanFactory->createAppDto($subscription->getSubscriptionPlan()));
        }
        if ($subscription->getPrice()) {
            $dto->setPrice($this->priceFactory->createAppDto($subscription->getPrice()));
        }
        $dto->setChildExternalReference($subscription->getChildExternalReference());
        $dto->setMainExternalReference($subscription->getMainExternalReference());
        $dto->setPaymentProviderDetailsUrl($subscription->getMainExternalReferenceDetailsUrl());
        $dto->setCreatedAt($subscription->getCreatedAt());
        $dto->setUpdatedAt($subscription->getUpdatedAt());
        $dto->setSeatNumber($subscription->getSeats());
        $dto->setValidUntil($subscription->getValidUntil());
        $dto->setCustomer($this->customerFactory->createAppDto($subscription->getCustomer()));
        $dto->setMetadata($subscription->getMetadata());

        return $dto;
    }

    public function createApiDto(Entity $subscription): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $subscription->getId());
        $dto->setPlan($this->subscriptionPlanFactory->createApiDto($subscription->getSubscriptionPlan()));
        if ($subscription->getPrice()) {
            $dto->setPrice($this->priceFactory->createApiDto($subscription->getPrice()));
            $dto->setSchedule($subscription->getPrice()->getSchedule());
        }
        $dto->setChildExternalReference($subscription->getChildExternalReference());
        $dto->setMainExternalReference($subscription->getMainExternalReference());
        $dto->setCreatedAt($subscription->getCreatedAt());
        $dto->setSeatNumber($subscription->getSeats());
        $dto->setUpdatedAt($subscription->getUpdatedAt());
        $dto->setValidUntil($subscription->getValidUntil());
        $dto->setStatus($subscription->getStatus()->value);
        $dto->setMetadata($subscription->getMetadata());

        return $dto;
    }
}
