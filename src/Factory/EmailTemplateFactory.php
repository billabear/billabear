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

namespace App\Factory;

use App\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use App\Dto\Request\App\EmailTemplate\UpdateEmailTemplate;
use App\Dto\Response\App\EmailTemplate\EmailTemplate as AppDto;
use App\Dto\Response\App\EmailTemplate\FullEmailTemplate as FullAppDto;
use App\Entity\EmailTemplate;
use App\Repository\BrandSettingRepositoryInterface;

class EmailTemplateFactory
{
    public function __construct(private BrandSettingRepositoryInterface $brandSettingRepository)
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
