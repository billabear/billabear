<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\UserInterface;

class CustomerRepository extends DoctrineCrudRepository implements CustomerRepositoryInterface
{
    public function getSubscriptionForUser(UserInterface $user): Subscription
    {
        return new Subscription();
    }

    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer
    {
        $customer = $this->entityRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException(sprintf("No customer found for email '%s'", $email));
        }

        return $customer;
    }

    public function hasCustomerByEmail(string $email): bool
    {
        try {
            $this->findByEmail($email);

            return true;
        } catch (NoEntityFoundException $e) {
            return false;
        }
    }

    public function getByExternalReference(string $externalReference): Customer
    {
        $customer = $this->entityRepository->findOneBy(['externalCustomerReference' => $externalReference]);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException(sprintf("No customer found for external reference '%s'", $externalReference));
        }

        return $customer;
    }

    public function getOldestCustomer(): Customer
    {
        $customer = $this->entityRepository->findOneBy([], ['createdAt' => 'ASC']);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException("Can't any any customer");
        }

        return $customer;
    }

    public function getCreatedCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('s');
        $queryBuilder->select('COUNT(s)')
            ->where($queryBuilder->expr()->gte('s.createdAt', ':startDate'))
            ->andWhere($queryBuilder->expr()->lte('s.createdAt', ':endDate'))
            ->andWhere('s.brandSettings = :brandSettings')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter(':brandSettings', $brandSettings);
        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        return intval($count);
    }

    public function getTotalCount(): int
    {
        return $this->entityRepository->count([]);
    }

    public function getLatestCustomers(int $number = 10): array
    {
        return $this->entityRepository->findBy([], ['createdAt' => 'DESC'], $number);
    }

    public function wipeCustomerSupportReferences(): void
    {
        $this->entityRepository->createQueryBuilder('s')
            ->update()
            ->set('s.customerSupportReference', ':customerSupportReference')
            ->setParameter('customerSupportReference', null)
            ->getQuery()
            ->execute();
    }

    public function wipeCrmReferences(): void
    {
        $this->entityRepository->createQueryBuilder('s')
            ->update()
            ->set('s.crmReference', ':crmReference')
            ->setParameter('crmReference', null)
            ->getQuery()
            ->execute();
    }

    public function wipeAccountingReferences(): void
    {
        $this->entityRepository->createQueryBuilder('s')
            ->update()
            ->set('s.accountingReference', ':accountingReference')
            ->setParameter('accountingReference', null)
            ->getQuery()
            ->execute();
    }

    public function wipeNewsletterReferences(): void
    {
        $this->entityRepository->createQueryBuilder('s')
            ->update()
            ->set('s.newsletterReference', ':newsletterReference')
            ->setParameter('newsletterReference', null)
            ->getQuery()
            ->execute();
    }
}
