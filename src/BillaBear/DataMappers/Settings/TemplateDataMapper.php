<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Generic\App\Template as AppDto;
use BillaBear\Dto\Request\App\Template\CreatePdfTemplate;
use BillaBear\Entity\Template;

class TemplateDataMapper
{
    public function createEntity(CreatePdfTemplate $createPdfTemplate): Template
    {
        $template = new Template();
        $template->setBrand($createPdfTemplate->getBrand());
        $template->setContent($createPdfTemplate->getTemplate());
        $template->setLocale($createPdfTemplate->getLocale());
        $template->setName($createPdfTemplate->getType());

        return $template;
    }

    public function createAppDto(Template $template): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $template->getId());
        $dto->setName($template->getName());
        $dto->setBrand($template->getBrand());
        $dto->setLocale($template->getLocale());

        return $dto;
    }
}
