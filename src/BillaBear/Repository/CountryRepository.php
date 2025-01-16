<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class CountryRepository extends DoctrineCrudRepository implements CountryRepositoryInterface
{
    public function getByIsoCode(mixed $value): Country
    {
        $country = $this->entityRepository->findOneBy(['isoCode' => $value]);

        if (!$country instanceof Country) {
            throw new NoEntityFoundException(sprintf('No country found for %s', $value));
        }

        return $country;
    }

    public function getAll()
    {
        return $this->entityRepository->findAll();
    }

    public function hasWithIsoCode(mixed $value): bool
    {
        try {
            $this->getByIsoCode($value);
        } catch (NoEntityFoundException) {
            return false;
        }

        return true;
    }
}
