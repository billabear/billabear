<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\PaymentFailureProcess;
use Parthenon\Common\Repository\DoctrineRepository;

class PaymentFailureProcessRepository extends DoctrineRepository implements PaymentFailureProcessRepositoryInterface
{
    public function findActiveForCustomer(\App\Entity\Customer $customer): ?PaymentFailureProcess
    {
        $qb = $this->entityRepository->createQueryBuilder('pfp');
        $qb->where('customer = :customer')
            ->andWhere($qb->expr()->notIn('state', ['payment_complete', 'payment_failure_no_more_retries']))
            ->setParameter('customer', $customer);

        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }
}
