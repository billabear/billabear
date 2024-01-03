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

namespace App\Repository;

use App\Entity\Checkout;
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
