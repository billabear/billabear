<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

/**
 * @method Country findById($id)
 * @method Country getById($id, $includeDeleted = false)
 */
interface CountryRepositoryInterface extends CrudRepositoryInterface
{
    public function hasWithIsoCode(mixed $value): bool;

    public function getByIsoCode(mixed $value): Country;

    /**
     * @return Country[]
     */
    public function getAll();

    public function getCountForRegistrationRequired(): int;

    public function getCountForCollecting(): int;

    public function getTotalCount(): int;
}
