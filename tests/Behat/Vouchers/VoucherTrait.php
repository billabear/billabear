<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Vouchers;

use BillaBear\Entity\Voucher;

trait VoucherTrait
{
    /**
     * @throws \Exception
     */
    public function getVoucher($voucherName): Voucher
    {
        $voucher = $this->voucherRepository->findOneBy(['name' => $voucherName]);

        if (!$voucher instanceof Voucher) {
            throw new \Exception('No voucher found');
        }

        $this->voucherRepository->getEntityManager()->refresh($voucher);

        return $voucher;
    }
}
