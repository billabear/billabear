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

namespace App\Tests\Behat\Developer;

use App\Entity\WebhookEndpoint;
use App\Repository\Orm\WebhookEndpointRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class WebhookContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private WebhookEndpointRepository $webhookRepository
    ) {
    }

    /**
     * @When I create a webhook with the following information:
     */
    public function iCreateAWebhookWithTheFollowingInformation(TableNode $table)
    {
        $row = $table->getRowsHash();
        $payload = [
            'name' => $row['Name'],
            'url' => $row['URL'],
        ];

        $this->sendJsonRequest('POST', '/app/developer/webhook', $payload);
    }

    /**
     * @Then there should be a webhook for the URL :arg1
     */
    public function thereShouldBeAWebhookForTheUrl($url)
    {
        $webhookEndpoint = $this->webhookRepository->findOneBy(['url' => $url]);

        if (!$webhookEndpoint instanceof WebhookEndpoint) {
            throw new \Exception('Webhook endpoint not found');
        }
    }

    /**
     * @Then there should not be a webhook for the URL :arg1
     */
    public function thereShouldNotBeAWebhookForTheUrl($url)
    {
        $webhookEndpoint = $this->webhookRepository->findOneBy(['url' => $url]);

        if ($webhookEndpoint instanceof WebhookEndpoint) {
            throw new \Exception('Webhook endpoint found');
        }
    }

    /**
     * @Given the following webhook endpoints exist:
     */
    public function theFollowingWebhookEndpointsExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $webhookEndpoint = new WebhookEndpoint();
            $webhookEndpoint->setName($row['Name']);
            $webhookEndpoint->setUrl($row['URL']);

            $webhookEndpoint->setCreatedAt(new \DateTime());
            $webhookEndpoint->setUpdatedAt(new \DateTime());
            $webhookEndpoint->setActive(true);

            $this->webhookRepository->getEntityManager()->persist($webhookEndpoint);
        }
        $this->webhookRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the webhook list
     */
    public function iViewTheWebhookList()
    {
        $this->sendJsonRequest('GET', '/app/developer/webhook');
    }

    /**
     * @Then I should see the webhook :arg1 in the list
     */
    public function iShouldSeeTheWebhookInTheList($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $item) {
            if ($item['name'] == $name) {
                return;
            }
        }

        throw new \Exception("Can't see webhook");
    }
}
