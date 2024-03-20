<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
