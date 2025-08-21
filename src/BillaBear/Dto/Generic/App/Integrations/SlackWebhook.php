<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Integrations;

readonly class SlackWebhook
{
    public function __construct(
        public string $id,
        public string $name,
        public string $webhook,
        public bool $enabled,
    ) {
    }
}
