<?php

namespace App\Repository;

use App\Entity\Customer;
use Parthenon\Common\Exception\NoEntityFoundException;

interface CustomerRepositoryInterface extends \Parthenon\Billing\Repository\CustomerRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer;

    public function hasCustomerByEmail(string $email): bool;
}
