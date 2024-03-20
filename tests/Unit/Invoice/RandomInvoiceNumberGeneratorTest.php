<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Invoice;

use App\Invoice\Number\RandomInvoiceNumberGenerator;
use PHPUnit\Framework\TestCase;

class RandomInvoiceNumberGeneratorTest extends TestCase
{
    public function testRandom()
    {
        $subject = new RandomInvoiceNumberGenerator();
        $this->assertMatchesRegularExpression('~[a-z0-9]{2}-\d+~', $subject->generate());
    }
}
