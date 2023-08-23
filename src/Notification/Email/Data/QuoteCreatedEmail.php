<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Quote;
use App\Entity\QuoteLine;

class QuoteCreatedEmail extends AbstractEmailData
{
    public function __construct(private Quote $quote, private string $fullUrl)
    {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_QUOTE_CREATED;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'quote' => $this->getQuoteData(),
        ];
    }

    private function getQuoteData(): array
    {
        return [
            'total' => $this->quote->getTotal(),
            'sub_total' => $this->quote->getSubTotal(),
            'tax_total' => $this->quote->getTaxTotal(),
            'currency' => $this->quote->getCurrency(),
            'lines' => array_map([$this, 'getQuoteLineData'], $this->quote->getLines()->toArray()),
            'pay_link' => $this->fullUrl,
        ];
    }

    private function getQuoteLineData(QuoteLine $quoteLine): array
    {
        return [
            'total' => $quoteLine->getTotal(),
            'sub_total' => $quoteLine->getSubTotal(),
            'tax_total' => $quoteLine->getTaxTotal(),
            'tax_percentage' => $quoteLine->getTaxPercentage(),
            'description' => $quoteLine->getDescription(),
        ];
    }
}
