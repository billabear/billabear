<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
