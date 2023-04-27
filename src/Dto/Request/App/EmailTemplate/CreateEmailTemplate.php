<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\EmailTemplate;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
class CreateEmailTemplate
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $locale;

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

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->useEmspTemplate) {
            if (empty($this->templateId)) {
                $context->addViolation('must have template id when using emsp template');
            }
        } else {
            if (empty($this->templateBody)) {
                $context->addViolation('must have template body when not using emsp template');
            }
            if (empty($this->subject)) {
                $context->addViolation('must have subject when not using emsp template');
            }
        }
    }
}
