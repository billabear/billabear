<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Repository\Orm\GenericBackgroundTaskRepository;
use App\Repository\Orm\SettingsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class TaxSettingsContext implements Context
{
    use SendRequestTrait;
    use SettingsTrait;

    public function __construct(
        private Session $session,
        private SettingsRepository $settingsRepository,
        private GenericBackgroundTaskRepository $genericBackgroundTaskRepository,
    ) {
    }

    /**
     * @When I update the tax system settings to:
     */
    public function iUpdateTheTaxSystemSettingsTo(TableNode $table)
    {
        $data = $table->getRowsHash();
        $taxCustomersWithTaxNumbers = 'true' === strtolower($data['Tax Customers with Tax Number'] ?? 'true');
        $euBusinessTaxRules = 'true' === strtolower($data['EU Business Tax Rules'] ?? 'true');
        $this->sendJsonRequest('POST', '/app/settings/tax', ['tax_customers_with_tax_number' => $taxCustomersWithTaxNumbers, 'eu_business_tax_rules' => $euBusinessTaxRules]);
    }

    /**
     * @Then the tax settings should be tax customers with tax number is false
     */
    public function theTaxSettingsShouldBeTaxCustomersWithTaxNumberIsFalse()
    {
        $settings = $this->getSettings();

        if ($settings->getTaxSettings()->getTaxCustomersWithTaxNumbers()) {
            throw new \Exception('Set to tax customers with tax numbers');
        }
    }

    /**
     * @Then the tax settings should be tax customers with tax number is true
     */
    public function theTaxSettingsShouldBeTaxCustomersWithTaxNumberIsTrue()
    {
        $settings = $this->getSettings();

        if (!$settings->getTaxSettings()->getTaxCustomersWithTaxNumbers()) {
            throw new \Exception('Set to not tax customers with tax numbers');
        }
    }

    /**
     * @Then the tax settings for eu business tax rules should be false
     */
    public function theTaxSettingsForEuBusinessTaxRulesShouldBeFalse()
    {
        $settings = $this->getSettings();

        if ($settings->getTaxSettings()->getEuropeanBusinessTaxRules()) {
            throw new \Exception('EU business tax rules enabled');
        }
    }

    /**
     * @Then the tax settings for eu business tax rules should be true
     */
    public function theTaxSettingsForEuBusinessTaxRulesShouldBeTrue()
    {
        $settings = $this->getSettings();

        if (!$settings->getTaxSettings()->getEuropeanBusinessTaxRules()) {
            throw new \Exception('EU business tax rules disabled');
        }
    }
}
