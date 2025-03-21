<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\Dto\Generic\App\Quote as AppDto;
use BillaBear\Dto\Generic\App\QuoteLine as AppLineDto;
use BillaBear\Dto\Generic\Public\Quote as PublicDto;
use BillaBear\Dto\Generic\Public\QuoteLine as PublicLineDto;
use BillaBear\Entity\Quote as Entity;
use BillaBear\Entity\QuoteLine as EntityLine;
use BillaBear\Quotes\PayLinkGeneratorInterface;

class QuoteDataMapper
{
    public function __construct(
        private BillingAdminDataMapper $billingAdminDataMapper,
        private CustomerDataMapper $customerDataMapper,
        private SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        private PriceDataMapper $priceDataMapper,
        private PayLinkGeneratorInterface $payLinkGenerator,
    ) {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setCreatedAt($entity->getCreatedAt());
        $appDto->setCreatedBy($this->billingAdminDataMapper->createAppDto($entity->getCreatedBy()));
        $appDto->setCustomer($this->customerDataMapper->createAppDto($entity->getCustomer()));
        $appDto->setId((string) $entity->getId());
        $appDto->setCurrency($entity->getCurrency());
        $appDto->setTotal($entity->getTotal());
        $appDto->setTaxTotal($entity->getTaxTotal());
        $appDto->setSubTotal($entity->getSubTotal());
        $appDto->setLines(array_map([$this, 'createAppLineDto'], $entity->getLines()->toArray()));
        $appDto->setPayLink($this->payLinkGenerator->generatePayLink($entity));
        $appDto->setPaid($entity->isPaid());
        $appDto->setPaidAt($entity->getPaidAt());
        $appDto->setExpiresAt($entity->getExpiresAt());

        return $appDto;
    }

    public function createPublicDto(Entity $entity): PublicDto
    {
        $now = new \DateTime();

        $publicDto = new PublicDto();
        $publicDto->setCreatedAt($entity->getCreatedAt());
        $publicDto->setCustomer($this->customerDataMapper->createPublicDto($entity->getCustomer()));
        $publicDto->setId((string) $entity->getId());
        $publicDto->setCurrency($entity->getCurrency());
        $publicDto->setTotal($entity->getTotal());
        $publicDto->setTaxTotal($entity->getTaxTotal());
        $publicDto->setSubTotal($entity->getSubTotal());
        $publicDto->setLines(array_map([$this, 'createPublicLineDto'], $entity->getLines()->toArray()));
        $publicDto->setPaid($entity->isPaid());
        $publicDto->setExpiresAt($entity->getExpiresAt());
        $publicDto->setExpired(null !== $entity->getExpiresAt() && $now > $entity->getExpiresAt());

        return $publicDto;
    }

    protected function createAppLineDto(EntityLine $quoteLine): AppLineDto
    {
        $appLineDto = new AppLineDto();
        if ($quoteLine->getSubscriptionPlan()) {
            $appLineDto->setSubscriptionPlan($this->subscriptionPlanDataMapper->createAppDto($quoteLine->getSubscriptionPlan()));
        }

        if ($quoteLine->getPrice()) {
            $appLineDto->setPrice($this->priceDataMapper->createAppDto($quoteLine->getPrice()));
        }
        $appLineDto->setDescription($quoteLine->getDescription());
        $appLineDto->setTotal($quoteLine->getTotal());
        $appLineDto->setSubTotal($quoteLine->getSubTotal());
        $appLineDto->setTaxTotal($quoteLine->getTaxTotal());
        $appLineDto->setCurrency($quoteLine->getCurrency());
        $appLineDto->setTaxRate($quoteLine->getTaxPercentage());
        $appLineDto->setSeatNumber($quoteLine->getSeatNumber());

        return $appLineDto;
    }

    protected function createPublicLineDto(EntityLine $quoteLine): PublicLineDto
    {
        $appLineDto = new PublicLineDto();
        if ($quoteLine->getSubscriptionPlan()) {
            $appLineDto->setSubscriptionPlan($this->subscriptionPlanDataMapper->createPublicDto($quoteLine->getSubscriptionPlan()));
        }

        if ($quoteLine->getPrice()) {
            $appLineDto->setPrice($this->priceDataMapper->createPublicDto($quoteLine->getPrice()));
        }
        $appLineDto->setDescription($quoteLine->getDescription());
        $appLineDto->setTotal($quoteLine->getTotal());
        $appLineDto->setSubTotal($quoteLine->getSubTotal());
        $appLineDto->setTaxTotal($quoteLine->getTaxTotal());
        $appLineDto->setCurrency($quoteLine->getCurrency());
        $appLineDto->setTaxRate($quoteLine->getTaxPercentage());
        $appLineDto->setSeatNumber($quoteLine->getSeatNumber());

        return $appLineDto;
    }
}
