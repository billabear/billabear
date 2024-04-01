<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Event;

use App\Entity\Quote;

class QuoteCreated
{
    public const NAME = 'billabear.quote.created';

    public function __construct(private Quote $quote)
    {
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }
}
