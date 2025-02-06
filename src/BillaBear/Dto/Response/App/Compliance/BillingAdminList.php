<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Compliance;

use BillaBear\Dto\Generic\App\BillingAdmin;
use BillaBear\Dto\Response\App\ListResponse;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class BillingAdminList
{
    public function __construct(
        #[SerializedName('billing_admin')]
        public BillingAdmin $billingAdmin,
        public ListResponse $list,
    ) {
    }
}
