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
}
