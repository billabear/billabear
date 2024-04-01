<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Import\Stripe;

use App\DataMappers\CreditDataMapper;
use App\Entity\Customer;
use App\Repository\CreditRepositoryInterface;
use Obol\Provider\ProviderInterface;

class CustomerCreditImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CreditDataMapper $creditFactory,
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
