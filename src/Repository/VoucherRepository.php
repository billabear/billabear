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
