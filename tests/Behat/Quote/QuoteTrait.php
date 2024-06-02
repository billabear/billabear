<?php

/*
 * Copyright all rights reserved. No public license given.
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
