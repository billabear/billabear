<?php

namespace App\Tests\Behat\Payments;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class MainContext implements Context
{
    public function __construct(private Session $session)
    {
    }
}
