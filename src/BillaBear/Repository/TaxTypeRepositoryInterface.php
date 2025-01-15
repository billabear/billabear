<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

/**
 * @method TaxType findById($id)
 */
interface TaxTypeRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return TaxType[]
     */
    public function getAll(): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getByName(string $name): TaxType;

    public function removeDefault(): void;

    public function getDefault(): TaxType;
}
