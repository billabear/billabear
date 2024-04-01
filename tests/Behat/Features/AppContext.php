<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Features;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Repository\Orm\SubscriptionFeatureServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use FeatureTrait;

    public function __construct(
        private Session $session,
        private SubscriptionFeatureServiceRepository $subscriptionFeatureRepository,
    ) {
    }

    /**
     * @When I create a feature via the APP with the info:
     */
    public function iCreateAFeatureViaTheAppWithTheInfo(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
            'name' => $data['Name'],
            'code' => $data['Code'],
            'description' => $data['Description'] ?? null,
        ];

        $this->sendJsonRequest('POST', '/app/feature', $payload);
    }

    /**
     * @Then there should be a feature with the code :arg1
     */
    public function thereShouldBeAFeatureWithTheCode($code)
    {
        $feature = $this->subscriptionFeatureRepository->findOneBy(['code' => $code]);

        if (!$feature instanceof SubscriptionFeature) {
            throw new \Exception('No feature found');
        }
    }

    /**
     * @Then there should be a feature with the name :arg1
     */
    public function thereShouldBeAFeatureWithTheName($name)
    {
        $this->getFeatureByName($name);
    }

    /**
     * @Given the following features exist:
     */
    public function theFollowingFeaturesExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $feature = new SubscriptionFeature();
            $feature->setName($row['Name']);
            $feature->setCode($row['Code']);
            $feature->setDescription($row['Description'] ?? null);

            $this->subscriptionFeatureRepository->getEntityManager()->persist($feature);
        }
        $this->subscriptionFeatureRepository->getEntityManager()->flush();
    }

    /**
     * @When I get the list of features via the APP
     */
    public function iGetTheListOfFeaturesViaTheApp()
    {
        $this->sendJsonRequest('GET', '/app/feature');
    }
}
