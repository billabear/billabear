<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Repository;

use App\Entity\Voucher;
use App\Enum\VoucherEvent;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface VoucherRepositoryInterface extends CrudRepositoryInterface
{
    public function getActiveByCode(string $code): Voucher;

    public function getActiveByEvent(VoucherEvent $event): ?Voucher;
}
