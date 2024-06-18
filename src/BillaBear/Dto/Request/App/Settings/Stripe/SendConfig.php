<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\Stripe;

use BillaBear\Validator\Constraints\Integrations\ValidStripeConfig;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ValidStripeConfig]
class SendConfig
{
    #[SerializedName('public_key')]
    private $publicKey;

    #[SerializedName('private_key')]
    private $privateKey;

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function setPublicKey($publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function setPrivateKey($privateKey): void
    {
        $this->privateKey = $privateKey;
    }
}
