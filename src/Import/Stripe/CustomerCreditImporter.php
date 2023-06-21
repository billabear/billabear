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

namespace App\Import\Stripe;

use App\Entity\Customer;
use App\Factory\CreditFactory;
use App\Repository\CreditRepositoryInterface;
use Obol\Provider\ProviderInterface;

class CustomerCreditImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CreditFactory $creditFactory,
        private CreditRepositoryInterface $repository, )
    {
    }

    public function importForCustomer(Customer $customer): void
    {
        $limit = 25;
        $lastId = null;
        do {
            $credits = $this->provider->credit()->getAllForCustomer($customer->getExternalCustomerReference(), $limit, $lastId);

            foreach ($credits as $balanceTransaction) {
                $credit = $this->creditFactory->createFromObol($customer, $balanceTransaction);
                $customer->addCreditAsMoney($credit->asMoney());
                $this->repository->save($credit);
            }
        } while (sizeof($credits) == $limit);
    }
}
