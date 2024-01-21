<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\CountryTaxRule as AppDto;
use App\Dto\Request\App\Country\CreateCountryTaxRule;
use App\Entity\Country;
use App\Entity\CountryTaxRule as Entity;
use App\Repository\TaxTypeRepositoryInterface;

class CountryTaxRuleDataMapper
{
    public function __construct(
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private TaxTypeDataMapper $taxTypeDataMapper,
    ) {
    }

    public function createEntity(CreateCountryTaxRule $countryTaxRule, Country $country): Entity
    {
        $taxType = $this->taxTypeRepository->findById($countryTaxRule->getTaxType());
        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $countryTaxRule->getValidFrom());

        $entity = new Entity();
        $entity->setCountry($country);
        $entity->setTaxType($taxType);
        $entity->setTaxRate(floatval($countryTaxRule->getTaxRate()));
        $entity->setIsDefault($countryTaxRule->getDefault());
        $entity->setValidFrom($validFrom);
        $entity->setCreatedAt(new \DateTime());

        return $entity;
    }

    public function createAppDto(Entity $countryTaxRule): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $countryTaxRule->getId());
        $dto->setTaxType($this->taxTypeDataMapper->createAppDto($countryTaxRule->getTaxType()));
        $dto->setTaxRate($countryTaxRule->getTaxRate());
        $dto->setValidFrom($countryTaxRule->getValidFrom());
        $dto->setValidUntil($countryTaxRule->getValidUntil());

        return $dto;
    }
}
