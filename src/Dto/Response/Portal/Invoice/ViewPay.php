<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\Portal\Invoice;

class ViewPay
{
    protected Invoice $invoice;

    protected StripeInfo $stripe;

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getStripe(): StripeInfo
    {
        return $this->stripe;
    }

    public function setStripe(StripeInfo $stripe): void
    {
        $this->stripe = $stripe;
    }
}
