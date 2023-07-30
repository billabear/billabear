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
}
