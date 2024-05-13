<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\Feature as AppDto;
use BillaBear\Dto\Request\BillaBear\PostFeature;
use Parthenon\Billing\Entity\SubscriptionFeature;

class FeatureDataMapper
{
    public function createFromPostFeature(PostFeature $postFeature): SubscriptionFeature
    {
        $feature = new SubscriptionFeature();
        $feature->setName($postFeature->getName());
        $feature->setCode($postFeature->getCode());
        $feature->setDescription($postFeature->getDescription());

        return $feature;
    }

    public function createAppDto(SubscriptionFeature $feature): AppDto
    {
        $appDto = new AppDto();
        $appDto->setId((string) $feature->getId());
        $appDto->setName($feature->getName());
        $appDto->setCode($feature->getCode());
        $appDto->setDescription($feature->getCode());

        return $appDto;
    }
}
