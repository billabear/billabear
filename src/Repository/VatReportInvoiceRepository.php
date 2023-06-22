<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

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
        $qb->select('SUM(vri.vatTotal) as totalVat, vri.currency, vri.payeeAddress.country as countryCode')
            ->groupBy('vri.currency, vri.payeeAddress.country')
            ->orderBy('vri.payeeAddress.country')
            ->where('vri.paidAt >= :paidAtStart')
            ->andWhere('vri.paidAt <= :paidAtEnd')
            ->andWhere('vri.vatTotal != 0')
            ->setParameter('paidAtStart', $start)
            ->setParameter('paidAtEnd', $end);

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}