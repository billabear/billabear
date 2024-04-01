<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Developer;

use App\Entity\WebhookEndpoint;
use App\Enum\WebhookEventType;
use App\Repository\Orm\WebhookEndpointRepository;
use App\Repository\Orm\WebhookEventRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class WebhookContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private WebhookEndpointRepository $webhookEndpointRepository,
        private WebhookEventRepository $webhookEventRepository,
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
        $webhookEndpoint = $this->webhookEndpointRepository->findOneBy(['url' => $url]);

        if (!$webhookEndpoint instanceof WebhookEndpoint) {
            throw new \Exception('Webhook endpoint not found');
        }
    }

    /**
     * @Then there should not be a webhook for the URL :arg1
     */
    public function thereShouldNotBeAWebhookForTheUrl($url)
    {
        $webhookEndpoint = $this->webhookEndpointRepository->findOneBy(['url' => $url]);

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

            $this->webhookEndpointRepository->getEntityManager()->persist($webhookEndpoint);
        }
        $this->webhookEndpointRepository->getEntityManager()->flush();
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

    /**
     * @When I view the webhook endpoint :arg1
     */
    public function iViewTheWebhook($name)
    {
        $webhook = $this->getWebhookEndpoint($name);

        $this->sendJsonRequest('GET', '/app/developer/webhook/'.$webhook->getId().'/view');
    }

    /**
     * @Then I should see the webhook url is :arg1
     */
    public function iShouldSeeTheWebhookUrlIs($url)
    {
        $data = $this->getJsonContent();

        if ($data['webhook_endpoint']['url'] != $url) {
            throw new \Exception('Not the correct url - '.$url);
        }
    }

    protected function getWebhookEndpoint(string $name): WebhookEndpoint
    {
        $entity = $this->webhookEndpointRepository->findOneBy(['name' => $name]);

        if (!$entity instanceof WebhookEndpoint) {
            throw new \Exception('Unable to find webhook for '.$name);
        }

        $this->webhookEndpointRepository->getEntityManager()->refresh($entity);

        return $entity;
    }

    /**
     * @Then there should be a webhook event for payment received
     */
    public function thereShouldBeAWebhookEventForPaymentReceived()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::PAYMENT_RECEIVED]);

        if (!$entity) {
            throw new \Exception("Can't find event");
        }
    }

    /**
     * @Then there should be a webhook event for customer enabled
     */
    public function thereShouldBeAWebhookEventForCustomerEnabled()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::CUSTOMER_ENABLED]);

        if (!$entity) {
            throw new \Exception("Can't find event");
        }
    }

    /**
     * @Then there should be a webhook event for customer disabled
     */
    public function thereShouldBeAWebhookEventForCustomerDisabled()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::CUSTOMER_DISABLED]);

        if (!$entity) {
            throw new \Exception("Can't find event");
        }
    }

    /**
     * @Then there should be a webhook event for customer created
     */
    public function thereShouldBeAWebhookEventForCustomerCreated()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::CUSTOMER_CREATED]);

        if (!$entity) {
            throw new \Exception("Can't find event");
        }
    }

    /**
     * @Then there should not be a webhook event for customer created
     */
    public function thereShouldNotBeAWebhookEventForCustomerCreated()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::CUSTOMER_CREATED]);

        if ($entity) {
            throw new \Exception('found event');
        }
    }

    /**
     * @Then there should be a webhook event for start subscription
     */
    public function thereShouldBeAWebhookEventForStartSubscription()
    {
        $entity = $this->webhookEventRepository->findOneBy(['type' => WebhookEventType::SUBSCRIPTION_CREATED]);

        if (!$entity) {
            throw new \Exception("Can't find event");
        }
    }
}
