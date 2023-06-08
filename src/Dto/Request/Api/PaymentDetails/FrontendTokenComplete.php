<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\Api\PaymentDetails;

use Symfony\Component\Validator\Constraints as Assert;

class FrontendTokenComplete
{
    #[Assert\NotBlank()]
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
