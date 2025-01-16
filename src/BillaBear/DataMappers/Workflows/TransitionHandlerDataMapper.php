<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Workflows;

use BillaBear\Dto\Generic\App\Workflows\TransitionHandler as AppDto;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerInterface;

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
