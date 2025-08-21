<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Tests\Behat\SendRequestTrait;

class ReportContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session)
    {
    }

    /**
     * @When I view the tax report page
     */
    public function iViewTheTaxReportPage()
    {
        $this->sendJsonRequest('GET', '/app/tax/report');
    }

    /**
     * @Then I will not see a tax item for :arg1
     */
    public function iWillNotSeeATaxItemFor($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['latest_tax_items'] as $taxItem) {
            if ($taxItem['customer_email'] === $arg1) {
                throw new \Exception('Tax item found');
            }
        }
    }

    /**
     * @Then I will see a tax item for :arg1
     */
    public function iWillSeeATaxItemFor($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['latest_tax_items'] as $taxItem) {
            if ($taxItem['customer_email'] === $arg1) {
                return;
            }
        }

        throw new \Exception('No tax item found');
    }

    /**
     * @Then I will see that the country :arg1 in list of active countries
     */
    public function iWillSeeThatTheCountryInListOfActiveCountries($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['active_countries'] as $taxItecountry) {
            if ($taxItecountry['country']['iso_code'] === $arg1) {
                return;
            }
        }

        throw new \Exception('No country found');
    }
}
