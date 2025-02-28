<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use BillaBear\Dto\Request\App\EmailTemplate\UpdateEmailTemplate;
use BillaBear\Dto\Response\App\EmailTemplate\EmailTemplate as AppDto;
use BillaBear\Dto\Response\App\EmailTemplate\FullEmailTemplate as FullAppDto;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Repository\BrandSettingsRepositoryInterface;

class EmailTemplateDataMapper
{
    public function __construct(private BrandSettingsRepositoryInterface $brandSettingRepository)
    {
    }

    public function createEntity(CreateEmailTemplate $dto): EmailTemplate
    {
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName($dto->getName());
        $emailTemplate->setLocale($dto->getLocale());
        $emailTemplate->setUseEmspTemplate($dto->getUseEmspTemplate());
        $emailTemplate->setTemplateId($dto->getTemplateId());
        $emailTemplate->setSubject($dto->getSubject());
        $emailTemplate->setTemplateBody($dto->getTemplateBody());

        $brand = $this->brandSettingRepository->getByCode($dto->getBrand());
        $emailTemplate->setBrand($brand);

        return $emailTemplate;
    }

    public function updateEntity(UpdateEmailTemplate $dto, EmailTemplate $emailTemplate): EmailTemplate
    {
        $emailTemplate->setUseEmspTemplate($dto->getUseEmspTemplate());
        $emailTemplate->setTemplateId($dto->getTemplateId());
        $emailTemplate->setSubject($dto->getSubject());
        $emailTemplate->setTemplateBody($dto->getTemplateBody());

        return $emailTemplate;
    }

    public function createAppDto(EmailTemplate $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setLocale($entity->getLocale());
        $dto->setBrand($entity->getBrand()->getBrandName());

        return $dto;
    }

    public function createFullAppDto(EmailTemplate $entity): FullAppDto
    {
        $dto = new FullAppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setLocale($entity->getLocale());
        $dto->setUseEmspTemplate($entity->isUseEmspTemplate());
        $dto->setSubject($entity->getSubject());
        $dto->setTemplateBody($entity->getTemplateBody());
        $dto->setTemplateId($entity->getTemplateId());
        $dto->setBrand($entity->getBrand()->getBrandName());

        return $dto;
    }
}
