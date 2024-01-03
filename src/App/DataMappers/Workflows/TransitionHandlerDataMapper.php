<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Workflows;

use App\Dto\Generic\App\Workflows\TransitionHandler as AppDto;
use App\Workflow\TransitionHandlers\DynamicTransitionHandlerInterface;

class TransitionHandlerDataMapper
{
    public function createAppDto(DynamicTransitionHandlerInterface $dynamicHandler): AppDto
    {
        $dto = new AppDto();
        $dto->setName($dynamicHandler->getName());
        $dto->setOptions($dynamicHandler->getOptions());

        return $dto;
    }
}
