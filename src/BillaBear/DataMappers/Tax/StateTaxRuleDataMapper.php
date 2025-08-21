<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Tax;

use BillaBear\Dto\Generic\App\StateTaxRule as AppDto;
use BillaBear\Dto\Request\App\Country\CreateStateTaxRule;
use BillaBear\Dto\Request\App\Country\UpdateStateTaxRule;
use BillaBear\Entity\StateTaxRule as Entity;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;

class StateTaxRuleDataMapper
{
    public function __construct(
        private StateRepositoryInterface $stateRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private TaxTypeDataMapper $taxTypeDataMapper,
    ) {
    }

    public function createEntity(CreateStateTaxRule|UpdateStateTaxRule $createStateTaxRule, ?Entity $entity = null): Entity
    {
        $taxType = $this->taxTypeRepository->findById($createStateTaxRule->getTaxType());
        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createStateTaxRule->getValidFrom());
        if (!$validFrom) {
            $validFrom = \DateTime::createFromFormat(\DATE_ATOM, $createStateTaxRule->getValidFrom());
        }
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }
        $entity->setState($this->stateRepository->getById($createStateTaxRule->getState()));
        $entity->setTaxType($taxType);
        $entity->setValidFrom($validFrom);
        $entity->setIsDefault($createStateTaxRule->isDefault());
        $entity->setTaxRate(floatval($createStateTaxRule->getTaxRate()));

        if ($createStateTaxRule->getValidUntil()) {
            $validUntil = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createStateTaxRule->getValidUntil());
            if (!$validUntil) {
                $validUntil = \DateTime::createFromFormat(\DATE_ATOM, $createStateTaxRule->getValidUntil());
            }

            $entity->setValidUntil($validUntil);
        } else {
            $entity->setValidUntil(null);
        }

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setTaxType($this->taxTypeDataMapper->createAppDto($entity->getTaxType()));
        $dto->setTaxRate(floatval($entity->getTaxRate()));
        $dto->setValidFrom($entity->getValidFrom());
        $dto->setValidUntil($entity->getValidUntil());
        $dto->setIsDefault($entity->isIsDefault());

        return $dto;
    }
}
