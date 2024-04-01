<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
