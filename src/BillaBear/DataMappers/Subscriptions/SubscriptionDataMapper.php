<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Subscriptions;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\Dto\Generic\Api\Subscription as ApiDto;
use BillaBear\Dto\Generic\App\Subscription as AppDto;
use BillaBear\Dto\Generic\Public\Subscription as PublicDto;
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
        $planDto = null;
        $priceDto = null;
        $schedule = $subscription->getPrice()?->getSchedule();

        if ($subscription->getSubscriptionPlan()) {
            $planDto = $this->subscriptionPlanFactory->createAppDto($subscription->getSubscriptionPlan());
        }

        if ($subscription->getPrice()) {
            $priceDto = $this->priceFactory->createAppDto($subscription->getPrice());
        }

        $dto = new AppDto(
            (string) $subscription->getId(),
            $subscription->getPaymentSchedule(),
            $subscription->getStatus()->value,
            $subscription->getSeats(),
            $subscription->getCreatedAt(),
            $subscription->getUpdatedAt(),
            $subscription->getEndedAt(),
            $subscription->getValidUntil(),
            $subscription->getMainExternalReference(),
            $subscription->getChildExternalReference(),
            $subscription->getMainExternalReferenceDetailsUrl(),
            $planDto,
            $priceDto,
            $this->customerFactory->createAppDto($subscription->getCustomer()),
            $subscription->getMetadata(),
        );

        return $dto;
    }

    public function createApiDto(Entity $subscription): ApiDto
    {
        $priceDto = null;
        $schedule = null;

        if ($subscription->getPrice()) {
            $priceDto = $this->priceFactory->createApiDto($subscription->getPrice());
            $schedule = $subscription->getPrice()->getSchedule();
        }
        $dto = new ApiDto(
            (string) $subscription->getId(),
            $schedule,
            $subscription->getCreatedAt(),
            $subscription->getUpdatedAt(),
            $subscription->getEndedAt(),
            $subscription->getValidUntil(),
            $subscription->getMainExternalReference(),
            $subscription->getChildExternalReference(),
            $this->subscriptionPlanFactory->createApiDto($subscription->getSubscriptionPlan()),
            $priceDto,
            $subscription->getSeats(),
            $subscription->getMetadata(),
            $subscription->getStatus()->value,
        );

        return $dto;
    }

    public function createPublicDto(Entity $subscription): PublicDto
    {
        $planDto = null;
        $priceDto = null;

        if ($subscription->getSubscriptionPlan()) {
            $planDto = $this->subscriptionPlanFactory->createPublicDto($subscription->getSubscriptionPlan());
        }

        if ($subscription->getPrice()) {
            $priceDto = $this->priceFactory->createPublicDto($subscription->getPrice());
        }

        return new PublicDto(
            (string) $subscription->getId(),
            $planDto,
            $priceDto,
            $subscription->getPaymentSchedule(),
            $subscription->getStatus(),
            $subscription->getCreatedAt(),
            $subscription->getValidUntil(),
        );
    }
}
