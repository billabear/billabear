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

namespace App\Dto\Request\App\Invoice;

use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInvoiceSubscription
{
    #[Assert\NotBlank()]
    #[SubscriptionPlanExists]
    private $plan;

    #[Assert\NotBlank]
    #[PriceExists]
    private $price;

    public function getPlan()
    {
        return $this->plan;
    }

    public function setPlan($plan): void
    {
        $this->plan = $plan;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }
}