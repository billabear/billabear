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
        $this->sendJsonRequest('POST', '/app/settings/tax', ['tax_customers_with_tax_number' => $taxCustomersWithTaxNumbers]);
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
}
