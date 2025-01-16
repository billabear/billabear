<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\PaymentDetails;

use Symfony\Component\Validator\Constraints as Assert;

class FrontendTokenComplete
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    protected $token;

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }
}
