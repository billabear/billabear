<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

use Obol\CreditServiceInterface;
use Obol\Model\Credit\BalanceOutput;
use Obol\Model\Credit\CreditTransaction;

class CreditService implements CreditServiceInterface
{
    public function addCreditTransaction(CreditTransaction $creditTransaction): BalanceOutput
    {
        $output = new BalanceOutput();
        $output->setId(bin2hex(random_bytes(16)));
        $output->setAmount($creditTransaction->getAmount());
        $output->setCurrency($creditTransaction->getCurrency());
        $output->setCustomerReference($creditTransaction->getCustomerReference());

        return $output;
    }

    public function getAllForCustomer(string $customerId, int $limit = 10, ?string $lastId = null): array
    {
        return [];
    }
}
