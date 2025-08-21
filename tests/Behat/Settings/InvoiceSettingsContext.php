<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Settings;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Invoice\InvoiceGenerationType;
use BillaBear\Repository\Orm\SettingsRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

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
        $this->sendJsonRequest('GET', '/app/invoice/settings');
        $settings = $this->getJsonContent()['invoice_settings'];
        $settings['invoice_number_generation'] = 'subsequential';
        $settings['subsequential_number'] = $count;
        $this->sendJsonRequest('POST', '/app/invoice/settings', $settings);
    }

    /**
     * @Given the subsequential invoice number is :arg1
     */
    public function theSubsequentialInvoiceNumberIs($arg1)
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setSubsequentialNumber(intval($arg1));
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given the invoice number generation is format :arg1
     */
    public function theInvoiceNumberGenerationIsFormat($arg1)
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceNumberGeneration('format');
        $settings->getSystemSettings()->setInvoiceNumberFormat($arg1);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
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
        $this->sendJsonRequest('GET', '/app/invoice/settings');
        $settings = $this->getJsonContent()['invoice_settings'];
        $settings['invoice_number_generation'] = 'random';
        $this->sendJsonRequest('POST', '/app/invoice/settings', $settings);
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

    /**
     * @Given the invoice number generation set to monthly
     */
    public function theInvoiceNumberGenerationSetToMonthly(): void
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceGenerationType(InvoiceGenerationType::PERIODICALLY);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the invoice generation to end of month
     */
    public function iUpdateTheInvoiceGenerationToEndOfMonth(): void
    {
        $this->sendJsonRequest('GET', '/app/invoice/settings');
        $settings = $this->getJsonContent()['invoice_settings'];
        $settings['invoice_generation'] = 'end_of_month';
        $this->sendJsonRequest('POST', '/app/invoice/settings', $settings);
    }

    /**
     * @Then the invoice generation should be set to end of month
     */
    public function theInvoiceGenerationShouldBeSetToEndOfMonth(): void
    {
        $settings = $this->getSettings();

        if (InvoiceGenerationType::END_OF_MONTH !== $settings->getSystemSettings()->getInvoiceGenerationType()) {
            throw new \Exception('Got - '.$settings->getSystemSettings()->getInvoiceGenerationType()->value);
        }
    }

    /**
     * @Given the invoice number generation set to end of month
     */
    public function theInvoiceNumberGenerationSetToEndOfMonth(): void
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setInvoiceGenerationType(InvoiceGenerationType::END_OF_MONTH);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the invoice generation to monthly
     */
    public function iUpdateTheInvoiceGenerationToMonthly(): void
    {
        $this->sendJsonRequest('GET', '/app/invoice/settings');
        $settings = $this->getJsonContent()['invoice_settings'];
        $settings['invoice_generation'] = 'periodically';
        $this->sendJsonRequest('POST', '/app/invoice/settings', $settings);
    }

    /**
     * @Then the invoice generation should be set to monthly
     */
    public function theInvoiceGenerationShouldBeSetToMonthly(): void
    {
        $settings = $this->getSettings();
        if (InvoiceGenerationType::PERIODICALLY !== $settings->getSystemSettings()->getInvoiceGenerationType()) {
            throw new \Exception('Got - '.$settings->getSystemSettings()->getInvoiceGenerationType()->value);
        }
    }
}
