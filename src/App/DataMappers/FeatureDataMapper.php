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

namespace App\DataMappers;

use App\Dto\Generic\App\Feature as AppDto;
use App\Dto\Request\App\PostFeature;
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
