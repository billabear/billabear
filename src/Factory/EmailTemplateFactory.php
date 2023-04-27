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
use App\Dto\Response\App\EmailTemplate\EmailTemplate as AppDto;
use App\Entity\EmailTemplate;

class EmailTemplateFactory
{
    public function createEntity(CreateEmailTemplate $dto): EmailTemplate
    {
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName($dto->getName());
        $emailTemplate->setLocale($dto->getLocale());
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

        return $dto;
    }
}
