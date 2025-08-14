<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterWebhook
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url(protocols: ['https'])]
        public ?string $url = null,
    ) {
    }
}
