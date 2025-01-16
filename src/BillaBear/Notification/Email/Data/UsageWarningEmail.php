<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email\Data;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\UsageLimit;
use Brick\Money\Money;

class UsageWarningEmail extends AbstractEmailData
{
    public function __construct(
        private UsageLimit $usageLimit,
        private Money $money,
    ) {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_USAGE_WARNING;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        $limitAmount = Money::ofMinor($this->usageLimit->getAmount(), $this->money->getCurrency());

        return [
            'customer' => $this->getCustomerData($customer),
            'brand' => $this->getBrandData($brandSettings),
            'limit_amount' => (string) $limitAmount,
            'current_amount' => (string) $this->money,
        ];
    }
}
