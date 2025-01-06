<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Integrations;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\SlackNotification;
use BillaBear\Entity\SlackWebhook;
use BillaBear\Notification\Slack\SlackNotificationEvent;
use BillaBear\Repository\Orm\SlackNotificationRepository;
use BillaBear\Repository\Orm\SlackWebhookRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class SlackIntegrationContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SlackWebhookRepository $slackWebhookRepository,
        private SlackNotificationRepository $slackNotificationRepository,
    ) {
    }

    /**
     * @When I create a slack webhook with:
     */
    public function iCreateASlackWebhookWith(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'webhook' => $data['Webhook'],
            'enabled' => 'true' === strtolower($data['Enabled'] ?? 'true'),
        ];
        $this->sendJsonRequest('POST', '/app/integrations/slack/webhook/create', $payload);
    }

    /**
     * @Then there will be a slack webhook called :arg1
     */
    public function getWebhookByName($name)
    {
        $slackWebhook = $this->slackWebhookRepository->findOneBy(['name' => $name]);

        if (!$slackWebhook instanceof SlackWebhook) {
            var_dump($this->getJsonContent());
            throw new \Exception(sprintf("Unable to find webhook for '%s'", $name));
        }
        $this->slackWebhookRepository->getEntityManager()->refresh($slackWebhook);

        return $slackWebhook;
    }

    /**
     * @Given the following slack webhooks exist:
     */
    public function theFollowingSlackWebhooksExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $slackWebhook = new SlackWebhook();
            $slackWebhook->setName($row['Name']);
            $slackWebhook->setWebhookUrl($row['Webhook']);
            $slackWebhook->setEnabled('true' === strtolower($row['Enabled'] ?? 'true'));
            $slackWebhook->setCreatedAt(new \DateTime());

            $this->slackWebhookRepository->getEntityManager()->persist($slackWebhook);
        }
        $this->slackWebhookRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the slack webhook list page
     */
    public function iGoToTheSlackWebhookListPage()
    {
        $this->sendJsonRequest('GET', '/app/integrations/slack/webhook');
    }

    /**
     * @Then I will see a webhook for :arg1
     */
    public function iWillSeeAWebhookFor($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $row) {
            if ($name === $row['name']) {
                return;
            }
        }

        throw new \Exception(sprintf("Unable to find webhook for '%s'", $name));
    }

    /**
     * @When I disable the :arg1 slack webhook
     */
    public function iDisableTheSlackWebhook($name)
    {
        $slackWebhook = $this->getWebhookByName($name);
        $this->sendJsonRequest('POST', '/app/integrations/slack/webhook/'.$slackWebhook->getId().'/disable');
    }

    /**
     * @Then the :arg1 slack webhook is not enabled
     */
    public function theSlackWebhookIsNotEnabled($name)
    {
        $slackWebhook = $this->getWebhookByName($name);
        if ($slackWebhook->isEnabled()) {
            throw new \Exception('This is enabled');
        }
    }

    /**
     * @Then the :arg1 slack webhook is enabled
     */
    public function theSlackWebhookIsEnabled($name)
    {
        $slackWebhook = $this->getWebhookByName($name);
        if (!$slackWebhook->isEnabled()) {
            throw new \Exception('This is not enabled');
        }
    }

    /**
     * @When I enable the :arg1 slack webhook
     */
    public function iEnableTheSlackWebhook($name)
    {
        $slackWebhook = $this->getWebhookByName($name);
        $this->sendJsonRequest('POST', '/app/integrations/slack/webhook/'.$slackWebhook->getId().'/enable');
    }

    /**
     * @When I create a slack notification rule:
     */
    public function iCreateASlackNotificationRule(TableNode $table)
    {
        $data = $table->getRowsHash();

        $webhook = $this->getWebhookByName($data['Webhook']);

        $payload = [
            'webhook' => (string) $webhook->getId(),
            'event' => $data['Event'],
            'template' => $data['Template'] ?? 'Template',
        ];
        $this->sendJsonRequest('POST', '/app/integrations/slack/notification/create', $payload);
    }

    /**
     * @Then there will be a slack notification rule for the webhook :arg1 and event :arg2
     */
    public function thereWillBeASlackNotificationRuleForTheWebhookAndEvent($webhookName, $event)
    {
        $webhook = $this->getWebhookByName($webhookName);
        $webEvent = SlackNotificationEvent::from($event);

        $slackNotification = $this->slackNotificationRepository->findOneBy(['slackWebhook' => $webhook, 'event' => $webEvent]);
        if (!$slackNotification instanceof SlackNotification) {
            throw new \Exception('Notification does not exist');
        }
        $this->slackNotificationRepository->getEntityManager()->refresh($slackNotification);
    }

    /**
     * @Given the following slack notifications exist:
     */
    public function theFollowingSlackNotificationsExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $slackNotification = new SlackNotification();
            $slackNotification->setSlackWebhook($this->getWebhookByName($row['Webhook']));
            $slackNotification->setEvent(SlackNotificationEvent::from($row['Event']));
            $slackNotification->setCreatedAt(new \DateTime());
            $slackNotification->setMessageTemplate($row['Template'] ?? 'template');
            $this->slackNotificationRepository->getEntityManager()->persist($slackNotification);
        }
        $this->slackNotificationRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the slack notification list page
     */
    public function iGoToTheSlackNotificationListPage()
    {
        $this->sendJsonRequest('GET', '/app/integrations/slack/notification');
    }

    /**
     * @Then I should see a slack notification in the list for :arg1 and event :arg2
     */
    public function iShouldSeeASlackNotificationInTheListForAndEvent($webhookName, $eventName)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $row) {
            if ($eventName === $row['event'] && $webhookName === $row['webhook']['name']) {
                return;
            }
        }
        throw new \Exception("Can't find notification");
    }

    /**
     * @When I go create a slack notification
     */
    public function iGoCreateASlackNotification()
    {
        $this->sendJsonRequest('GET', '/app/integrations/slack/notification/create');
    }

    /**
     * @Then I will see the slack webhook :arg1 will be in the list
     */
    public function iWillSeeTheSlackWebhookWillBeInTheList($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['webhooks'] as $row) {
            if ($name === $row['name']) {
                return;
            }
        }

        throw new \Exception(sprintf("Unable to find webhook for '%s'", $name));
    }
}
