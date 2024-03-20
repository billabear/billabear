<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\CountryTaxRule as AppDto;
use App\Dto\Request\App\Country\CreateCountryTaxRule;
use App\Dto\Request\App\Country\UpdateCountryTaxRule;
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

    public function createEntity(CreateCountryTaxRule|UpdateCountryTaxRule $countryTaxRule, Country $country, ?Entity $entity = null): Entity
    {
        $taxType = $this->taxTypeRepository->findById($countryTaxRule->getTaxType());
        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $countryTaxRule->getValidFrom());
        if (!$validFrom) {
            $validFrom = \DateTime::createFromFormat(\DATE_ATOM, $countryTaxRule->getValidFrom());
        }

        if (null === $entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }
        $entity->setCountry($country);
        $entity->setTaxType($taxType);
        $entity->setTaxRate(floatval($countryTaxRule->getTaxRate()));
        $entity->setIsDefault($countryTaxRule->getDefault());
        $entity->setValidFrom($validFrom);

        if ($countryTaxRule->getValidUntil()) {
            $validUntil = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $countryTaxRule->getValidUntil());
            if (!$validUntil) {
                $validUntil = \DateTime::createFromFormat(\DATE_ATOM, $countryTaxRule->getValidUntil());
            }

            $entity->setValidUntil($validUntil);
        } else {
            $entity->setValidUntil(null);
        }

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
        $dto->setIsDefault($countryTaxRule->isIsDefault());

        return $dto;
    }
}
