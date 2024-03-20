<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\Country;
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
}
