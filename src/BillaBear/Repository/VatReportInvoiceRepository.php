<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Parthenon\Common\Repository\CustomServiceRepository;

class VatReportInvoiceRepository implements VatReportRepositoryInterface
{
    public function __construct(private CustomServiceRepository $entityRepository)
    {
    }

    public function getDataForMonth(\DateTime $dateTime): array
    {
        $start = clone $dateTime;
        $start->modify('first day of this month');

        $end = clone $dateTime;
        $end->modify('last day of this month');
        $qb = $this->entityRepository->createQueryBuilder('vri');
        $qb->select('SUM(vri.taxTotal) as totalVat, vri.currency, vri.payeeAddress.country as countryCode')
            ->groupBy('vri.currency, vri.payeeAddress.country')
            ->orderBy('vri.payeeAddress.country')
            ->where('vri.paidAt >= :paidAtStart')
            ->andWhere('vri.paidAt <= :paidAtEnd')
            ->andWhere('vri.taxTotal != 0')
            ->setParameter('paidAtStart', $start)
            ->setParameter('paidAtEnd', $end);

        return $qb->getQuery()->getResult();
    }
}
