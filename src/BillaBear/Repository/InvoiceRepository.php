<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\Subscription;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\NoEntityFoundException;

class InvoiceRepository extends DoctrineCrudRepository implements InvoiceRepositoryInterface
{
    public function getAllForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC']);
    }

    public function getOverdueInvoices(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('i');
        $qb->where('i.paid = false')
            ->andWhere('i.dueAt < :now')
            ->setParameter(':now', new \DateTime('now'));

        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    public function getUnpaidInvoices(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('i');
        $qb->where('i.paid = false');

        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    public function getLatestForSubscription(Subscription $subscription): Invoice
    {
        $qb = $this->entityRepository->createQueryBuilder('i');

        $qb->where(':subscription MEMBER OF i.subscriptions')
            ->setParameter(':subscription', $subscription)
            ->orderBy('i.createdAt')
            ->setMaxResults(1);

        $query = $qb->getQuery();

        $entity = $query->getResult();

        if (!$entity instanceof Invoice) {
            throw new NoEntityFoundException(sprintf("Can't find invoice for subscription '%s'", $subscription->getId()));
        }
    }

    public function getLastTenForCustomer(Customer $customer): ResultSet
    {
        $results = $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC'], 11);

        return new ResultSet($results, 'createdAt', 'DESC', 10);
    }

    public function getLastForCustomer(Customer $customer): ?Invoice
    {
        return $this->entityRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);
    }

    public function getTotalCount(): int
    {
        return $this->entityRepository->count([]);
    }
}
