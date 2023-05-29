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

namespace App\Repository\Processes;

use App\Entity\Customer;
use App\Entity\Processes\ExpiringCardProcess;
use Doctrine\ORM\NoResultException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class ExpiringCardProcessRepository extends DoctrineRepository implements ExpiringCardProcessRepositoryInterface
{
    public function getActiveProccesses(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('ec');
        $qb->where('ec.state != :completedState')
            ->andWhere('ec.state != :cardAdded')
            ->setParameter('cardAdded', 'card_added')
            ->setParameter('completedState', 'completed');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getActiveProcessForCustomer(Customer $customer): ExpiringCardProcess
    {
        $qb = $this->entityRepository->createQueryBuilder('ec');
        $qb->where('ec.state != :completedState')
            ->andWhere('ec.state != :cardAdded')
            ->andWhere('ec.customer = :customer')
            ->setParameter('customer', $customer)
            ->setParameter('cardAdded', 'card_added')
            ->setParameter('completedState', 'completed');

        try {
            $active = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new NoEntityFoundException();
        }
        if (!$active instanceof ExpiringCardProcess) {
            throw new NoEntityFoundException();
        }

        return $active;
    }
}
