<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Interopt\Stripe;

use App\Dto\Interopt\Stripe\Models\ListModel;
use App\Dto\Interopt\Stripe\Models\Subscription as Model;
use App\Entity\Subscription as Entity;
use App\Repository\InvoiceRepositoryInterface;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionDataMapper
{
    public function __construct(
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private SubscriptionItemDataMapper $subscriptionItemDataMapper,
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    public function createModel(Entity $entity): Model
    {
        $model = new Model();
        $model->setId((string) $entity->getId());
        $model->setCancelAtPeriodEnd(SubscriptionStatus::PENDING_CANCEL === $entity->getStatus());
        $model->setCurrency(strtolower($entity->getCurrency()));
        $model->setCurrentPeriodEnd($entity->getValidUntil()->getTimestamp());
        $model->setCurrentPeriodStart($entity->getStartOfCurrentPeriod()->getTimestamp());
        $model->setCustomer($entity->getCustomer()->getId());

        $paymentCard = $this->paymentCardRepository->getDefaultPaymentCardForCustomer($entity->getCustomer());
        $model->setDefaultPaymentMethod((string) $paymentCard->getId());
        $model->setDescription(null);

        $list = new ListModel();
        $list->setData([$this->subscriptionItemDataMapper->createModel($entity)]);
        $list->setHasMore(true);
        $list->setUrl('/v1/subscription_items?subscription='.$entity->getId());
        $model->setItems($list);

        try {
            $invoice = $this->invoiceRepository->getLatestForSubscription($entity);
            $model->setLastInvoice((string) $invoice->getId());
        } catch (NoEntityFoundException $e) {
        }
        $model->setStatus($this->convertStatus($entity->getStatus()));
        $model->setMetadata(['plan_name' => $entity->getPlanName()]);

        return $model;
    }

    private function convertStatus(SubscriptionStatus $subscriptionStatus): string
    {
        return match ($subscriptionStatus) {
            SubscriptionStatus::ACTIVE, SubscriptionStatus::BLOCKED, SubscriptionStatus::PENDING_CANCEL => 'active',
            SubscriptionStatus::CANCELLED => 'cancelled',
            SubscriptionStatus::OVERDUE_PAYMENT_DISABLED, SubscriptionStatus::OVERDUE_PAYMENT_OPEN => 'past_due',
            default => 'incomplete_expired',
        };
    }
}