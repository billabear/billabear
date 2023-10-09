<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\EmailTemplate;

use Symfony\Component\Serializer\Annotation\SerializedName;

class FullEmailTemplate
{
    private string $id;

    private string $name;

    private string $locale;

    private string $brand;

    #[SerializedName('use_emsp_template')]
    private bool $useEmspTemplate;

    private ?string $subject;

    #[SerializedName('template_body')]
    private ?string $templateBody;

    #[SerializedName('template_id')]
    private ?string $templateId;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function isUseEmspTemplate(): bool
    {
        return $this->useEmspTemplate;
    }

    public function setUseEmspTemplate(bool $useEmspTemplate): void
    {
        $this->useEmspTemplate = $useEmspTemplate;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getTemplateBody(): ?string
    {
        return $this->templateBody;
    }

    public function setTemplateBody(?string $templateBody): void
    {
        $this->templateBody = $templateBody;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): void
    {
        $this->templateId = $templateId;
    }
}
