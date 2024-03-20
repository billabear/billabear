<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\EmailTemplate;
use Doctrine\ORM\NoResultException;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class EmailTemplateRepository extends DoctrineCrudRepository implements EmailTemplateRepositoryInterface
{
    public function getByNameAndLocale(string $name, string $locale): EmailTemplate
    {
        $emailTemplate = $this->entityRepository->findOneBy(['name' => $name, 'locale' => $locale]);

        if (!$emailTemplate instanceof EmailTemplate) {
            throw new NoEntityFoundException(sprintf("Can't find email template for name '%s' and locale '%s'", $name, $locale));
        }

        return $emailTemplate;
    }

    public function getByNameAndLocaleAndBrand(string $name, string $locale, string $brand): ?EmailTemplate
    {
        $qb = $this->entityRepository->createQueryBuilder('em');

        $qb->join('em.brand', 'b')
            ->where('em.name = :name')
            ->andWhere('em.locale = :locale')
            ->andWhere('b.code = :brand')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->setParameter('brand', $brand);
        $query = $qb->getQuery();

        try {
            $result = $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }

        if (!$result instanceof EmailTemplate) {
            return null;
        }

        return $result;
    }
}
