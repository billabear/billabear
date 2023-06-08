<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use Parthenon\Billing\Entity\Subscription;

interface SubscriptionRepositoryInterface extends \Parthenon\Billing\Repository\SubscriptionRepositoryInterface
{
    /**
     * @return Subscription[]
     */
    public function getSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return Subscription[]
     */
    public function getInvoiceSubscriptionsExpiringInNextFiveMinutes(): array;

    /**
     * @return Subscription[]
     */
    public function getAll(): array;

    /**
     * @return Subscription[]
     */
    public function getAllActive(): array;

    public function getCountActive(): int;
}
