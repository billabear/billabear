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

namespace App\Repository;

use App\Entity\Customer;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\UserInterface;

class CustomerRepository extends DoctrineCrudRepository implements CustomerRepositoryInterface
{
    public function getSubscriptionForUser(UserInterface $user): Subscription
    {
        return new Subscription();
    }

    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer
    {
        $customer = $this->entityRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException(sprintf("No customer found for email '%s'", $email));
        }

        return $customer;
    }

    public function hasCustomerByEmail(string $email): bool
    {
        try {
            $this->findByEmail($email);

            return true;
        } catch (NoEntityFoundException $e) {
            return false;
        }
    }

    public function getByExternalReference(string $externalReference): CustomerInterface
    {
        $customer = $this->entityRepository->findOneBy(['externalCustomerReference' => $externalReference]);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException(sprintf("No customer found for external reference '%s'", $externalReference));
        }

        return $customer;
    }
}
