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

use App\Repository\Orm\SettingsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class InvoiceSettingsContext implements Context
{
    use SendRequestTrait;
    use SettingsTrait;

    public function __construct(
        private Session $session,
        private SettingsRepository $settingsRepository,
    ) {
    }

    /**
     * @Given the invoice number generation is random
     */
    public function theInvoiceNumberGenerationIsRandom()
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceNumberGeneration('random');
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the invoice number generation to subsequential with the count of :arg1
     */
    public function iUpdateTheInvoiceNumberGenerationToSubsequentialWithTheCountOf($count)
    {
        $this->sendJsonRequest('GET', '/app/settings/system');
        $settings = $this->getJsonContent()['system_settings'];
        $settings['timezone'] = $this->getJsonContent()['timezones'][0];
        $settings['invoice_number_generation'] = 'subsequential';
        $settings['subsequential_number'] = $count;
        $this->sendJsonRequest('POST', '/app/settings/system', $settings);
    }

    /**
     * @Then the invoice number generation should be subsequential.
     */
    public function theInvoiceNumberGenerationShouldBeSubsequential()
    {
        $settings = $this->getSettings();

        if ('subsequential' != $settings->getSystemSettings()->getInvoiceNumberGeneration()) {
            throw new \Exception('Got - '.$settings->getSystemSettings()->getInvoiceNumberGeneration());
        }
    }

    /**
     * @Then the invoice subsequential number is :arg1
     */
    public function theInvoiceSubsequentialNumberIs($arg1)
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->getSubsequentialNumber() != $arg1) {
            throw new \Exception('Got - '.$settings->getSystemSettings()->getSubsequentialNumber());
        }
    }

    /**
     * @Given the invoice number generation is subsequential
     */
    public function theInvoiceNumberGenerationIsSubsequential()
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceNumberGeneration('subsequential');
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the invoice number generation to random
     */
    public function iUpdateTheInvoiceNumberGenerationToRandom()
    {
        $this->sendJsonRequest('GET', '/app/settings/system');
        $settings = $this->getJsonContent()['system_settings'];
        $settings['timezone'] = $this->getJsonContent()['timezones'][0];
        $settings['invoice_number_generation'] = 'random';
        $this->sendJsonRequest('POST', '/app/settings/system', $settings);
    }

    /**
     * @Then the invoice number generation should be random.
     */
    public function theInvoiceNumberGenerationShouldBeRandom()
    {
        $settings = $this->getSettings();

        if ('random' != $settings->getSystemSettings()->getInvoiceNumberGeneration()) {
            throw new \Exception('Got - '.$settings->getSystemSettings()->getInvoiceNumberGeneration());
        }
    }
}
