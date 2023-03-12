<?php

namespace App\Repository;

use App\Entity\Customer;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
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
     * @param string $email
     * @return Customer
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer {
        $customer = $this->entityRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            throw new NoEntityFoundException(sprintf("No customer found for email '%s'", $email));
        }

        return $customer;
    }

    public function hasCustomerByEmail(string $email) : bool
    {
        try {
            $this->findByEmail($email);
            return true;
        } catch (NoEntityFoundException $e) {
            return false;
        }
    }
}
