<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\Common\Collections\Collection;

interface ConvertableToInvoiceInterface
{
    public function getCustomer(): ?Customer;

    public function getCurrency(): string;

    /**
     * @return Collection|ConvertableToInvoiceLineInterface[]
     */
    public function getLines(): array|Collection;

    public function setSubscriptions(array|Collection $subscriptions);

    public function setUpdatedAt(\DateTime $dateTime);

    public function setPaid(bool $paid);

    public function setPaidAt(\DateTime $paidAt);
}
