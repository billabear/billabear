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

use App\Entity\ApiKey;
use App\Repository\Orm\ApiKeyRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class ApiKeyContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private ApiKeyRepository $apiKeyRepository,
    ) {
    }

    /**
     * @Given the follow api keys exist:
     */
    public function theFollowApiKeysExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $apiKey = new ApiKey();
            $apiKey->setKey($row['API Key']);
            $apiKey->setName($row['Name']);
            $apiKey->setExpiresAt(new \DateTime($row['Expires At']));
            $apiKey->setCreatedAt(new \DateTime());
            $apiKey->setUpdatedAt(new \DateTime());

            $this->apiKeyRepository->getEntityManager()->persist($apiKey);
        }
        $this->apiKeyRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the api keys list
     */
    public function iViewTheApiKeysList()
    {
        $this->sendJsonRequest('GET', '/app/settings/api-key');
    }

    /**
     * @Then I will see a api key with the name :arg1 and the key :arg2
     */
    public function iWillSeeAApiKeyWithTheNameAndTheKey($name, $key)
    {
        $json = $this->getJsonContent();

        foreach ($json['data'] as $keyData) {
            if ($keyData['name'] === $name && $keyData['key'] === $key) {
                return;
            }
        }

        throw new \Exception("Can't find API Key");
    }

    /**
     * @When I create an API key for the name :arg1 with the expires :arg2
     */
    public function iCreateAnApiKeyForTheNameWithTheExpires($name, $datestring)
    {
        $date = new \DateTime($datestring);
        $payload = [
            'name' => $name,
            'expires_at' => $date->format(\DATE_RFC3339_EXTENDED),
        ];

        $this->sendJsonRequest('POST', '/app/settings/api-key', $payload);
    }

    /**
     * @Then there will be an API key with the name :arg1
     */
    public function thereWillBeAnApiKeyWithTheName($arg1)
    {
        $apiKey = $this->apiKeyRepository->findOneBy(['name' => $arg1]);

        if (!$apiKey instanceof ApiKey) {
            throw new \Exception('No API Key found');
        }
    }

    /**
     * @Then there will not be an API key with the name :arg1
     */
    public function thereWillNotBeAnApiKeyWithTheName($arg1)
    {
        $apiKey = $this->apiKeyRepository->findOneBy(['name' => $arg1]);

        if ($apiKey instanceof ApiKey) {
            throw new \Exception('API Key found');
        }
    }
}
