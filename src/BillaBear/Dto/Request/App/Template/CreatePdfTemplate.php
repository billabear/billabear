<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Template;

use BillaBear\Entity\Template;
use BillaBear\Validator\Constraints\BrandCodeExists;
use BillaBear\Validator\Constraints\UniquePdfTemplate;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePdfTemplate]
class CreatePdfTemplate
{
    #[Assert\Locale]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $locale;

    #[Assert\Choice(choices: Template::NAMES)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $type;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[BrandCodeExists]
    private $brand;

    #[Assert\NotBlank]
    private $template;

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template): void
    {
        $this->template = $template;
    }
}
