<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Checkout;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

/**
 * @method getById($id, $includeDeleted = false) Checkout
 * @method findById($id) Checkout
 */
interface CheckoutRepositoryInterface extends CrudRepositoryInterface
{
    public function findBySlug(string $slug): Checkout;
}
