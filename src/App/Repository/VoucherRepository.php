<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\Voucher;
use App\Enum\VoucherEvent;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class VoucherRepository extends DoctrineCrudRepository implements VoucherRepositoryInterface
{
    public function getActiveByCode(string $code): Voucher
    {
        $voucher = $this->entityRepository->findOneBy(['code' => $code, 'disabled' => false]);

        if (!$voucher instanceof Voucher) {
            throw new NoEntityFoundException(sprintf("No voucher for '%s' found", $code));
        }

        return $voucher;
    }

    public function getActiveByEvent(VoucherEvent $event): ?Voucher
    {
        $voucher = $this->entityRepository->findOneBy(['entryEvent' => $event, 'disabled' => false]);

        if (!$voucher instanceof Voucher) {
            return null;
        }

        return $voucher;
    }
}
