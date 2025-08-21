<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Product;
use Parthenon\Billing\Repository\Orm\SubscriptionPlanRepository as BaseRepository;

class SubscriptionPlanRepository extends BaseRepository implements SubscriptionPlanRepositoryInterface
{
    public function getNonDeletedForProduct(Product $product): array
    {
        return $this->entityRepository->findBy(['product' => $product, 'isDeleted' => false]);
    }
}
