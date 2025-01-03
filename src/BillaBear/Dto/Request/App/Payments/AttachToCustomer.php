<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Payments;

use BillaBear\Validator\Constraints\CustomerExists;
use Symfony\Component\Validator\Constraints as Assert;

class AttachToCustomer
{
    #[Assert\NotBlank]
    #[CustomerExists]
    private $customer;

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }
}
