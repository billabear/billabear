<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
     * @Given the invoice number generation is subsequential with the count of :arg1
     */
    public function theInvoiceNumberGenerationIsSubsequentialWithTheCountOf($invoiceNumber)
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceNumberGeneration('subsequential');
        $settings->getSystemSettings()->setSubsequentialNumber(intval($invoiceNumber));
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
