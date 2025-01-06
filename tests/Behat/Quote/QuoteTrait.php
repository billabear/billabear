<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Quote;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Quote;

trait QuoteTrait
{
    protected function getLatestQuoteForCustomer(Customer $customer): Quote
    {
        $quote = $this->quoteRepository->findOneBy(['customer' => $customer]);
        if (!$quote) {
            throw new \Exception('Unable to find quote');
        }
        $this->quoteRepository->getEntityManager()->refresh($quote);

        return $quote;
    }
}
