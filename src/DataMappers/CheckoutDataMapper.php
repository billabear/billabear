<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Checkout\PortalLinkGenerator;
use App\Dto\Generic\App\Checkout as AppDto;
use App\Dto\Generic\App\CheckoutLine as AppLineDto;
use App\Dto\Generic\Public\Checkout as PublicDto;
use App\Dto\Generic\Public\CheckoutLine as PublicLineDto;
use App\Entity\Checkout as Entity;
use App\Entity\CheckoutLine as EntityLine;

class CheckoutDataMapper
{
    public function __construct(
        private BillingAdminDataMapper $billingAdminDataMapper,
        private CustomerDataMapper $customerDataMapper,
        private SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        private PriceDataMapper $priceDataMapper,
        private PortalLinkGenerator $portalLinkGenerator,
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

    public function createPublicDto(Entity $entity): PublicDto
    {
        $appDto = new PublicDto();
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

    protected function createPublicLineDto(EntityLine $quoteLine): PublicLineDto
    {
        $appLineDto = new PublicLineDto();
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
}