<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Settings;

use App\Dto\Generic\App\Template as AppDto;
use App\Entity\Template;

class TemplateDataMapper
{
    public function createAppDto(Template $template): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $template->getId());
        $dto->setName($template->getName());
        $dto->setGroup($template->getBrand());

        return $dto;
    }
}
