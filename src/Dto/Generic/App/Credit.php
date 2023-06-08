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

namespace App\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Credit
{
    private Customer $customer;

    #[SerializedName('billing_admin')]
    private ?BillingAdmin $billingAdmin = null;

    private int $amount;

    private string $currency;

    #[SerializedName('used_amount')]
    private int $usedAmount;

    private ?string $reason = null;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    private string $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getBillingAdmin(): ?BillingAdmin
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?BillingAdmin $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getUsedAmount(): int
    {
        return $this->usedAmount;
    }

    public function setUsedAmount(int $usedAmount): void
    {
        $this->usedAmount = $usedAmount;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
