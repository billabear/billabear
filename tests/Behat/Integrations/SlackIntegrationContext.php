<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Integrations;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\SlackWebhook;
use BillaBear\Repository\Orm\SlackWebhookRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class SlackIntegrationContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SlackWebhookRepository $slackWebhookRepository,
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
    public function thereWillBeASlackWebhookCalled($name)
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
        $slackWebhook = $this->thereWillBeASlackWebhookCalled($name);
        $this->sendJsonRequest('POST', '/app/integrations/slack/webhook/'.$slackWebhook->getId().'/disable');
    }

    /**
     * @Then the :arg1 slack webhook is not enabled
     */
    public function theSlackWebhookIsNotEnabled($name)
    {
        $slackWebhook = $this->thereWillBeASlackWebhookCalled($name);
        if ($slackWebhook->isEnabled()) {
            throw new \Exception('This is enabled');
        }
    }

    /**
     * @Then the :arg1 slack webhook is enabled
     */
    public function theSlackWebhookIsEnabled($name)
    {
        $slackWebhook = $this->thereWillBeASlackWebhookCalled($name);
        if (!$slackWebhook->isEnabled()) {
            throw new \Exception('This is not enabled');
        }
    }

    /**
     * @When I enable the :arg1 slack webhook
     */
    public function iEnableTheSlackWebhook($name)
    {
        $slackWebhook = $this->thereWillBeASlackWebhookCalled($name);
        $this->sendJsonRequest('POST', '/app/integrations/slack/webhook/'.$slackWebhook->getId().'/enable');
    }
}
