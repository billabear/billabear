<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Checkout;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class CheckoutRepository extends DoctrineCrudRepository implements CheckoutRepositoryInterface
{
    public function findBySlug(string $slug): Checkout
    {
        $checkout = $this->entityRepository->findOneBy(['slug' => $slug]);

        if (!$checkout instanceof Checkout) {
            throw new NoEntityFoundException(sprintf("Didn't find a checkout for slug '%s'", $slug));
        }

        return $checkout;
    }
}
