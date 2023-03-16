<?php

namespace App\Dto\Response\App;

use App\Dto\Generic\App\Customer;

class CustomerView
{
    protected Customer $customer;

    protected bool $success = true;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }
}
