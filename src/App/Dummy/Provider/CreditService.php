<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dummy\Provider;

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

    public function getAllForCustomer(string $customerId, int $limit = 10, string $lastId = null): array
    {
        return [];
    }
}
