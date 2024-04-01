<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository\Processes;

use App\Entity\Processes\InvoiceProcess;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class InvoiceProcessRepository extends DoctrineRepository implements InvoiceProcessRepositoryInterface
{
    public function getForInvoice(\App\Entity\Invoice $invoice): InvoiceProcess
    {
        $invoiceProcess = $this->entityRepository->findOneBy(['invoice' => $invoice]);

        if (!$invoiceProcess instanceof InvoiceProcess) {
            throw new NoEntityFoundException("Can't find an invoice process");
        }

        return $invoiceProcess;
    }

    public function getOverdueBy30days(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('ip');
        $qb->where('ip.dueAt < :deadline')
            ->andWhere('ip.state = :warnedState')
            ->setParameter('deadline', new \DateTime('-30 days'))
            ->setParameter('warnedState', 'customer_warning_sent');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
