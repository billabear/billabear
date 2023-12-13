<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
