<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\DataMappers\CreditDataMapper;
use BillaBear\Entity\Customer;
use BillaBear\Repository\CreditRepositoryInterface;
use Obol\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class CustomerCreditImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CreditDataMapper $creditFactory,
        private CreditRepositoryInterface $repository,
    ) {
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
