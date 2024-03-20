<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\Api\Subscription;

use Symfony\Component\Validator\Constraints as Assert;

class AddSeats
{
    #[Assert\NotBlank()]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $seats;

    public function getSeats()
    {
        return $this->seats;
    }

    public function setSeats($seats): void
    {
        $this->seats = $seats;
    }
}
