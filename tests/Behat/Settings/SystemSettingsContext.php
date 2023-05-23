<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

        if (isset($data['Timezone'])) {
            $systemSettings->setTimezone($data['Timezone']);
        }

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
        $data = $table->getRowsHash();
        $payload = [
            'webhook_url' => $data['Webhook URL'] ?? null,
            'timezone' => $data['Timezone'] ?? null,
        ];

        $this->sendJsonRequest('POST', '/app/settings/system', $payload);
    }

    /**
     * @Then the system settings for webhook url will be :arg1
     */
    public function theSystemSettingsForWebhookUrlWillBe($webhookUrl)
    {
        $settings = $this->getSettings();

        if ($settings->getSystemSettings()->getWebhookUrl() !== $webhookUrl) {
            var_dump($this->getJsonContent());
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
     * @Then I will see system settings for webhook url will be :arg1
     */
    public function iWillSeeSystemSettingsForWebhookUrlWillBe($webhookDomain)
    {
        $data = $this->getJsonContent();

        if ($data['system_settings']['webhook_url'] !== $webhookDomain) {
            throw new \Exception("Webhook domain doesn't match");
        }
    }
}
