<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Logger\Audit;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IndexProvider implements IndexProviderInterface
{
    public function __construct(
        #[Autowire(env: 'AUDIT_LOG_INDEX')]
        private string $index,
    ) {
    }

    public function getIndex(): string
    {
        return $this->index;
    }
}
