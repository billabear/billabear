<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\Stripe;

use BillaBear\Validator\Constraints\Integrations\ValidStripeConfig;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ValidStripeConfig]
readonly class SendConfig
{
    public function __construct(
        #[SerializedName('public_key')]
        public ?string $publicKey = null,
        #[SerializedName('private_key')]
        public ?string $privateKey = null,
    ) {
    }
}
