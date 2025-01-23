<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Interopt\Stripe\Requests\Subscriptions;

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
