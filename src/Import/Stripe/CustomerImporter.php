<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Factory\CustomerFactory;
use App\Repository\CustomerRepositoryInterface;
use Obol\Model\Customer;
use Obol\Provider\ProviderInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class CustomerImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CustomerFactory $factory,
        private CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function import(StripeImport $stripeImport)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = null;
        do {
            $customerList = $provider->customers()->list($limit, $lastId);
            /** @var Customer $customerModel */
            foreach ($customerList as $customerModel) {
                try {
                    $customer = $this->customerRepository->getByExternalReference($customerModel->getId());
                } catch (NoEntityFoundException $exception) {
                    $customer = null;
                }
                $customer = $this->factory->createCustomerFromObol($customerModel, $customer);
                $this->customerRepository->save($customer);
                $lastId = $customerModel->getId();
                var_dump($lastId);
            }
        } while (sizeof($customerList) == $limit);
    }
}