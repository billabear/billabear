<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
