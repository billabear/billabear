<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Interopt\Stripe\Requests\Subscriptions;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CancelSubscription
{
    private ?CancellationDetails $cancellationDetails = null;

    #[Assert\Type('boolean')]
    #[SerializedName('invoice_now')]
    private $invoiceNow;

    #[Assert\Type('boolean')]
    private $prorate;

    public function getCancellationDetails(): ?CancellationDetails
    {
        return $this->cancellationDetails;
    }

    public function setCancellationDetails(?CancellationDetails $cancellationDetails): void
    {
        $this->cancellationDetails = $cancellationDetails;
    }

    public function getInvoiceNow()
    {
        return $this->invoiceNow;
    }

    public function setInvoiceNow($invoiceNow): void
    {
        $this->invoiceNow = $invoiceNow;
    }

    public function getProrate()
    {
        return $this->prorate;
    }

    public function setProrate($prorate): void
    {
        $this->prorate = $prorate;
    }
}
