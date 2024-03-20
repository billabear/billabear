<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\EmailTemplate;

use App\Entity\EmailTemplate;
use App\Validator\Constraints\BrandCodeExists;
use App\Validator\Constraints\UniqueEmailTemplate;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
#[UniqueEmailTemplate]
class CreateEmailTemplate
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice(choices: EmailTemplate::TEMPLATE_NAMES)]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Locale]
    private $locale;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[BrandCodeExists]
    private $brand;

    #[Assert\Type('boolean')]
    #[SerializedName('use_emsp_template')]
    private $useEmspTemplate;

    private $subject;

    #[SerializedName('template_body')]
    private $templateBody;

    #[SerializedName('template_id')]
    private $templateId;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getUseEmspTemplate()
    {
        return $this->useEmspTemplate;
    }

    public function setUseEmspTemplate($useEmspTemplate): void
    {
        $this->useEmspTemplate = $useEmspTemplate;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    public function getTemplateBody()
    {
        return $this->templateBody;
    }

    public function setTemplateBody($templateBody): void
    {
        $this->templateBody = $templateBody;
    }

    public function getTemplateId()
    {
        return $this->templateId;
    }

    public function setTemplateId($templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->useEmspTemplate) {
            if (empty($this->templateId)) {
                $context->buildViolation('must have template id when using emsp template')->atPath('tenplate_id')->addViolation();
            }
        } else {
            if (empty($this->templateBody)) {
                $context->buildViolation('must have template body when not using emsp template')->atPath('template_body')->addViolation();
            }
            if (empty($this->subject)) {
                $context->buildViolation('must have subject when not using emsp template')->atPath('subject')->addViolation();
            }
        }
    }
}
