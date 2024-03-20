<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Voucher;

use App\Entity\Voucher as Entity;
use Obol\Model\Voucher\Amount;
use Obol\Model\Voucher\Voucher as ObolModel;
use Obol\Provider\ProviderInterface;

class VoucherRegister
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    public function register(Entity $entity)
    {
        $obol = new ObolModel();
        $obol->setType($entity->getType()->value);
        $obol->setPercentage($entity->getPercentage());
        $obol->setDuration('once');
        $obol->setCode($entity->getCode());
        $obol->setName($entity->getName());
        $amounts = [];
        foreach ($entity->getAmounts() as $amount) {
            $obolAmount = new Amount();
            $obolAmount->setAmount($amount->getAmount());
            $obolAmount->setCurrency($amount->getCurrency());
            $amounts[] = $obolAmount;
        }
        $obol->setAmounts($amounts);

        $voucherCreation = $this->provider->vouchers()->createVoucher($obol);
        $entity->setExternalReference($voucherCreation->getId());
    }
}
