<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\GenericBackgroundTask;
use App\Enum\GenericTask;
use App\Repository\Orm\GenericBackgroundTaskRepository;
use App\Repository\Orm\SettingsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class SystemSettingsContext implements Context
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
     * @Given the system settings are:
     */
    public function theSystemSettingsAre(TableNode $table)
    {
        $data = $table->getRowsHash();
        $settings = $this->getSettings();
        $systemSettings = $settings->getSystemSettings();

        if (isset($data['Webhook URL'])) {
            $systemSettings->setWebhookUrl($data['Webhook URL']);
        }
        if (isset($data['System URL'])) {
            $systemSettings->setSystemUrl($data['System URL']);
        }

        if (isset($data['Timezone'])) {
            $systemSettings->setTimezone($data['Timezone']);
        }

        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given that the tax settings for tax customers with tax number is false
     */
    public function thatTheTaxSettingsForTaxCustomersWithTaxNumberIsFalse()
    {
        $settings = $this->getSettings();
        $taxSettings = $settings->getTaxSettings();
        $taxSettings->setTaxCustomersWithTaxNumbers(false);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given that the tax settings for tax customers with tax number is true
     */
    public function thatTheTaxSettingsForTaxCustomersWithTaxNumberIsTrue()
    {
        $settings = $this->getSettings();
        $taxSettings = $settings->getTaxSettings();
        $taxSettings->setTaxCustomersWithTaxNumbers(true);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given that the tax settings for eu business tax rules is true
     */
    public function thatTheTaxSettingsForEuBusinessTaxRulesIsTrue()
    {
        $settings = $this->getSettings();
        $taxSettings = $settings->getTaxSettings();
        $taxSettings->setEuropeanBusinessTaxRules(true);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given that the tax settings for eu business tax rules is false
     */
    public function thatTheTaxSettingsForEuBusinessTaxRulesIsFalse()
    {
        $settings = $this->getSettings();
        $taxSettings = $settings->getTaxSettings();
        $taxSettings->setEuropeanBusinessTaxRules(false);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given stripe billing is disabled
     */
    public function stripeBillingIsDisabled()
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setUseStripeBilling(false);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I register the webhook url :arg1
     */
    public function iRegisterTheWebhookUrl($webhookUrl)
    {
        $this->sendJsonRequest('POST', '/app/settings/stripe/webhook/register', ['url' => $webhookUrl]);
    }

    /**
     * @Then the system settings will have a webhook id
     */
    public function theSystemSettingsWillHaveAWebhookId()
    {
        $settings = $this->getSettings();

        if (!$settings->getSystemSettings()->getWebhookExternalReference()) {
            throw new \Exception('No webhook external reference');
        }
    }

    /**
     * @Given the system settings for main currency is :arg1
     */
    public function theSystemSettingsForMainCurrencyIs($arg1)
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setMainCurrency($arg1);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @Given the webhook url is set for :arg1
     */
    public function theWebhookUrlIsSetFor($arg1)
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setSystemUrl($arg1);
        $settings->getSystemSettings()->setWebhookExternalReference('dsjdj');
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I deregister my webhook
     */
    public function iDeregisterMyWebhook()
    {
        $this->sendJsonRequest('POST', '/app/settings/stripe/webhook/deregister');
    }

    /**
     * @Then the system settings will not have a webhook id
     */
    public function theSystemSettingsWillNotHaveAWebhookId()
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->getWebhookExternalReference()) {
            throw new \Exception('webhook external reference');
        }
    }

    /**
     * @When I disable stripe billing
     */
    public function iDisableStripeBilling()
    {
        $this->sendJsonRequest('POST', '/app/settings/stripe/disable-billing');
    }

    /**
     * @Then there should be a stripe billing cancel task scheduled
     */
    public function thereShouldBeAStripeBillingCancelTaskScheduled()
    {
        $task = $this->genericBackgroundTaskRepository->findOneBy(['task' => GenericTask::CANCEL_STRIPE_BILLING]);

        if (!$task instanceof GenericBackgroundTask) {
            throw new \Exception('No background task found');
        }
    }

    /**
     * @Then stripe billing should be disabled
     */
    public function stripeBillingShouldBeDisabled()
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->isUseStripeBilling()) {
            throw new \Exception('Stripe billing should be disabled');
        }
    }

    /**
     * @When I enable stripe billing
     */
    public function iEnableStripeBilling()
    {
        $this->sendJsonRequest('POST', '/app/settings/stripe/enable-billing');
    }

    /**
     * @Then stripe billing should be enabled
     */
    public function stripeBillingShouldBeEnabled()
    {
        $settings = $this->getSettings();

        if (!$settings->getSystemSettings()->isUseStripeBilling()) {
            throw new \Exception('Stripe billing should be enabled');
        }
    }

    /**
     * @Given stripe billing is enabled
     */
    public function stripeBillingIsEnabled()
    {
        $settings = $this->getSettings();
        $settings->getSystemSettings()->setUseStripeBilling(true);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the system settings to:
     */
    public function iUpdateTheSystemSettingsTo(TableNode $table)
    {
        $this->sendJsonRequest('GET', '/app/settings/system');
        $settings = $this->getJsonContent()['system_settings'];
        $data = $table->getRowsHash();
        $settings['system_url'] = $data['System URL'] ?? null;
        $settings['timezone'] = $data['Timezone'] ?? null;

        $this->sendJsonRequest('POST', '/app/settings/system', $settings);
    }

    /**
     * @Then the system settings for webhook url will be :arg1
     */
    public function theSystemSettingsForWebhookUrlWillBe($webhookUrl)
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->getWebhookUrl() !== $webhookUrl) {
            throw new \Exception("Webhook url doesn't match");
        }
    }

    /**
     * @Then the system settings for system url will be :arg1
     */
    public function theSystemSettingsForSystemUrlWillBe($webhookUrl)
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->getSystemUrl() !== $webhookUrl) {
            throw new \Exception("Webhook url doesn't match");
        }
    }

    /**
     * @Then the system settings for webhook will be nullified
     */
    public function theSystemSettingsForWebhookUrlWillBeNullified()
    {
        $settings = $this->getSettings();

        if (null !== $settings->getSystemSettings()->getWebhookUrl()) {
            throw new \Exception("Webhook url doesn't match");
        }
    }

    /**
     * @When I fetch the system settings
     */
    public function iFetchTheSystemSettings()
    {
        $this->sendJsonRequest('GET', '/app/settings/system');
    }

    /**
     * @Then I will see system settings for system url will be :arg1
     */
    public function iWillSeeSystemSettingsForWebhookUrlWillBe($webhookDomain)
    {
        $data = $this->getJsonContent();

        if ($data['system_settings']['system_url'] !== $webhookDomain) {
            throw new \Exception("system domain doesn't match");
        }
    }
}
