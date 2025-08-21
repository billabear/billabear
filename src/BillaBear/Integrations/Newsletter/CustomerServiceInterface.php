<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter;

use BillaBear\Entity\Customer;

interface CustomerServiceInterface
{
    public function register(string $listId, Customer $customer): CustomerRegistration;

    /**
     * Reference and subscribe are passed because there is the announcement list and marketing.
     *
     * In theory both should be usable.
     */
    public function update(string $listId, string $reference, bool $subscribe, Customer $customer): void;

    public function isSubscribed(string $listId, string $reference): bool;
}
