<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Entity\CustomerInterface;

interface ConvertableToInvoiceInterface
{
    public function getCustomer(): ?CustomerInterface;

    public function getCurrency(): string;

    /**
     * @return Collection|ConvertableToInvoiceLineInterface[]
     */
    public function getLines(): Collection|array;

    public function setSubscriptions(array|Collection $subscriptions);

    public function setUpdatedAt(\DateTime $dateTime);

    public function setPaid(bool $paid);

    public function setPaidAt(\DateTime $paidAt);
}
