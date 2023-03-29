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
        $feature = $this->subscriptionFeatureRepository->findOneBy(['name' => $name]);

        if (!$feature instanceof SubscriptionFeature) {
            throw new \Exception('No feature found');
        }
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
}
