<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory\Settings;

use App\Dto\Generic\App\Template as AppDto;
use App\Entity\Template;

class TemplateFactory
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
