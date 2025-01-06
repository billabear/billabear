<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Integrations;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class Integration
{
    public function __construct(
        public string $name,
        #[SerializedName('authentication_type')]
        public string $authenticationType,
        public array $settings,
    ) {
    }
}
