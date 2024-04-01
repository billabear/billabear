<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\EmailTemplate;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CreateEmailTemplate
{
    #[SerializedName('template_names')]
    private array $templateNames;

    #[SerializedName('brands')]
    private array $brands;

    public function getTemplateNames(): array
    {
        return $this->templateNames;
    }

    public function setTemplateNames(array $templateNames): void
    {
        $this->templateNames = $templateNames;
    }

    public function getBrands(): array
    {
        return $this->brands;
    }

    public function setBrands(array $brands): void
    {
        $this->brands = $brands;
    }
}
