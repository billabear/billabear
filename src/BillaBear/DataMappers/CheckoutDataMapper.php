<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Checkout\PortalLinkGeneratorInterface;
use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\Dto\Generic\App\Checkout as AppDto;
use BillaBear\Dto\Generic\App\CheckoutLine as AppLineDto;
use BillaBear\Dto\Generic\Public\Checkout as PublicDto;
use BillaBear\Dto\Generic\Public\CheckoutLine as PublicLineDto;
use BillaBear\Entity\Checkout as Entity;
use BillaBear\Entity\CheckoutLine as EntityLine;

class CheckoutDataMapper
{
    public function __construct(
        private BillingAdminDataMapper $billingAdminDataMapper,
        private CustomerDataMapper $customerDataMapper,
        private SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        private PriceDataMapper $priceDataMapper,
        private PortalLinkGeneratorInterface $portalLinkGenerator,
    ) {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setName($entity->getName());
        $appDto->setCreatedAt($entity->getCreatedAt());
        $appDto->setCreatedBy($this->billingAdminDataMapper->createAppDto($entity->getCreatedBy()));
        $appDto->setCustomer($this->customerDataMapper->createAppDto($entity->getCustomer()));
        $appDto->setId((string) $entity->getId());
        $appDto->setCurrency($entity->getCurrency());
        $appDto->setTotal($entity->getTotal());
        $appDto->setTaxTotal($entity->getTaxTotal());
        $appDto->setSubTotal($entity->getSubTotal());
        $appDto->setLines(array_map([$this, 'createAppLineDto'], $entity->getLines()->toArray()));
        $appDto->setExpiresAt($entity->getExpiresAt());
        $appDto->setPayLink($this->portalLinkGenerator->generatePayLink($entity));

        return $appDto;
    }

    public function createPublicDto(Entity $entity): PublicDto
    {
        return new PublicDto(
            (string) $entity->getId(),
            $entity->getName(),
            $entity->getCurrency(),
            $this->customerDataMapper->createAppDto($entity->getCustomer()),
            $entity->getTotal(),
            $entity->getSubTotal(),
            $entity->getTaxTotal(),
            array_map([$this, 'createPublicLineDto'], $entity->getLines()->toArray()),
            $entity->getCreatedAt(),
            $this->portalLinkGenerator->generatePayLink($entity),
            $entity->getExpiresAt()
        );
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
        $plan = null;
        $price = null;
        if ($quoteLine->getSubscriptionPlan()) {
            $plan = $this->subscriptionPlanDataMapper->createAppDto($quoteLine->getSubscriptionPlan());
        }

        if ($quoteLine->getPrice()) {
            $price = $this->priceDataMapper->createAppDto($quoteLine->getPrice());
        }

        return new PublicLineDto(
            $plan,
            $price,
            $quoteLine->getDescription(),
            $quoteLine->getCurrency(),
            $quoteLine->getTotal(),
            $quoteLine->getSeatNumber(),
            $quoteLine->getSubTotal(),
            $quoteLine->getTaxTotal(),
            $quoteLine->getTaxPercentage(),
            $quoteLine->getPrice()?->getSchedule() ?? 'one-off',
        );
    }
}
