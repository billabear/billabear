<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Compliance;

use BillaBear\Dto\Generic\App\Subscription;
use BillaBear\Dto\Response\App\ListResponse;

readonly class SubscriptionList
{
    public function __construct(
        public Subscription $subscription,
        public ListResponse $list,
    ) {
    }
}
