<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\Portal\Customer;

use BillaBear\Dto\Generic\Public\Customer;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class MainView
{
    public function __construct(
        public Customer $customer,
        public array $subscriptions,
        #[SerializedName('payment_methods')]
        public array $paymentMethods,
        public array $invoices,
    ) {
    }
}
