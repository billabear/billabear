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
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class NotificationSettingsContext implements Context
{
    use SettingsTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SettingsRepository $settingsRepository,
    ) {
    }

    /**
     * @Given the notification settings are:
     */
    public function theNotificationSettingsAre(TableNode $table)
    {
        $settings = $this->getSettings();

        $data = $table->getRowsHash();

        $notificationSettings = $settings->getNotificationSettings();
        $notificationSettings->setEmsp($data['EMSP'] ?? null);
        $notificationSettings->setEmspApiKey($data['API Key'] ?? null);
        $notificationSettings->setEmspApiUrl($data['API URL'] ?? null);
        $notificationSettings->setEmspDomain($data['Domain'] ?? null);
        $notificationSettings->setDefaultOutgoingEmail($data['Outgoing Email'] ?? null);
        $notificationSettings->setSendCustomerNotifications('false' === strtolower($data['Send Customer Notification'] ?? 'false'));

        $settings->setNotificationSettings($notificationSettings);
        $this->settingsRepository->getEntityManager()->persist($settings);
        $this->settingsRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the notification settings to:
     */
    public function iUpdateTheNotificationSettingsTo(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
            'emsp' => $data['EMSP'] ?? null,
            'emsp_api_key' => $data['API Key'] ?? null,
            'emsp_api_url' => $data['API URL'] ?? null,
            'emsp_domain' => $data['Domain'] ?? null,
            'default_outgoing_email' => $data['Outgoing Email'] ?? null,
            'send_customer_notifications' => ('false' === strtolower($data['Send Customer Notification'] ?? 'false')),
        ];

        $this->sendJsonRequest('POST', '/app/settings/notification-settings', $payload);
    }

    /**
     * @When I view the notification settings
     */
    public function iViewTheNotificationSettings()
    {
        $this->sendJsonRequest('GET', '/app/settings/notification-settings');
    }

    /**
     * @Then I will see that the notification settings for EMSP is :arg1
     */
    public function iWillSeeThatTheNotificationSettingsForEmspIs($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['notification_settings']['emsp'] !== $arg1) {
            throw new \Exception('Notification setting EMSP is different');
        }
    }

    /**
     * @Then the notification settings for EMSP will be :arg1
     */
    public function theNotificationSettingsForEmspWillBe($emsp)
    {
        $data = $this->getJsonContent();
        $settings = $this->getSettings();

        if ($settings->getNotificationSettings()->getEmsp() !== $emsp) {
            throw new \Exception('Notification setting EMSP is different');
        }
    }
}
