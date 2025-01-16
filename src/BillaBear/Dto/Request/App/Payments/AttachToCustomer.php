<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
