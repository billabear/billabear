<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Api;

use App\Entity\ApiKey;
use App\Repository\Orm\ApiKeyRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Symfony\Component\HttpFoundation\Response;

class MainContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private ApiKeyRepository $apiKeyRepository)
    {
    }

    /**
     * @Then I should be told there is a conflict
     */
    public function iShouldBeToldThereIsAConflict()
    {
        if (Response::HTTP_CONFLICT !== $this->session->getStatusCode()) {
            throw new \Exception('No conflict response given');
        }
    }

    /**
     * @Then I should not be told there is a conflict
     */
    public function iShouldNotBeToldThereIsAConflict()
    {
        if (Response::HTTP_CONFLICT === $this->session->getStatusCode()) {
            throw new \Exception('There conflict response given');
        }
    }

    /**
     * @Given I have authenticated to the API
     */
    public function iHaveAuthenticatedToTheApi()
    {
        $key = bin2hex(random_bytes(24));

        $this->authenticate($key);

        $apiKey = new ApiKey();
        $apiKey->setName('Behat');
        $apiKey->setKey($key);
        $apiKey->setActive(true);
        $apiKey->setExpiresAt(new \DateTime('+1 hour'));
        $apiKey->setCreatedAt(new \DateTime('now'));
        $apiKey->setUpdatedAt(new \DateTime());

        $this->apiKeyRepository->getEntityManager()->persist($apiKey);
        $this->apiKeyRepository->getEntityManager()->flush();
    }

    /**
     * @Then there should be an error for :arg1
     */
    public function thereShouldBeAnErrorFor($errorKey)
    {
        $data = $this->getJsonContent();

        if (!isset($data['errors'])) {
            var_dump($data);
            throw new \Exception('No errors');
        }
        if (!isset($data['errors'][$errorKey])) {
            var_dump($data);
            throw new \Exception('No error');
        }
    }

    /**
     * @Then there should not be an error
     */
    public function thereShouldNotBeAnError()
    {
        $data = $this->getJsonContent();

        if (isset($data['errors'])) {
            var_dump($data['errors']);
            throw new \Exception('Errors found');
        }
    }

    /**
     * @Then there should not be an error for :arg1
     */
    public function thereShouldNotBeAnErrorFor($errorKey)
    {
        $data = $this->getJsonContent();

        if (!isset($data['errors'])) {
            throw new \Exception('No errors');
        }

        if (isset($data['errors'][$errorKey])) {
            throw new \Exception('Error found');
        }
    }

    /**
     * @Then the API response data field should be empty
     */
    public function theApiResponseDataFieldShouldBeEmpty()
    {
        $data = $this->getJsonContent();

        if (count($data['data']) > 0) {
            throw new \Exception('Found values in the data field');
        }
    }

    /**
     * @Then I should see in the API response with only :arg1 result in the data set
     */
    public function iShouldSeeInTheApiResponseWithOnlyResultInTheDataSet($arg1)
    {
        $data = $this->getJsonContent();

        if (count($data['data']) != intval($arg1)) {
            throw new \Exception(sprintf('Found %d results instead of %d', count($data['data']), $arg1));
        }
    }

    /**
     * @Then the I should see in the API response there are more results
     */
    public function theIShouldSeeInTheApiResponseThereAreMoreResults()
    {
        $data = $this->getJsonContent();

        if (!$data['has_more']) {
            throw new \Exception('API Response does not say there are more');
        }
    }

    /**
     * @Then the I should not see in the API response there are more results
     */
    public function theIShouldNotSeeInTheApiResponseThereAreMoreResults()
    {
        $data = $this->getJsonContent();
        if ($data['has_more']) {
            throw new \Exception('API Response does say there are more');
        }
    }

    /**
     * @Then I will see the :arg1 data with the :arg2 value :arg3
     */
    public function iWillSeeTheDataWithTheValue($arg1, $arg2, $arg3)
    {
        $data = $this->getJsonContent();

        if (!isset($data[$arg1])) {
            throw new \Exception(sprintf('The key "%s" doesn\'t exist', $arg1));
        }

        if (!isset($data[$arg1][$arg2])) {
            throw new \Exception(sprintf('The key "%s" in "%s" doesn\'t exist', $arg2, $arg1));
        }

        if ($data[$arg1][$arg2] != $arg3) {
            throw new \Exception("Expected '%s' but got '%s'", $arg3, $data[$arg1][$arg2]);
        }
    }

    /**
     * @Then I will see the :arg1 contains :arg2 items
     */
    public function iWillSeeTheContainsItems($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        if (!isset($data[$arg1])) {
            throw new \Exception(sprintf('The key "%s" doesn\'t exist', $arg1));
        }

        if (!is_array($data[$arg1])) {
            throw new \Exception(sprintf('The key "%s" isn\'t an array', $arg1));
        }

        if (sizeof($data[$arg1]) != $arg2) {
            throw new \Exception(sprintf('The count for key "%s" isn\'t %d', $arg1, $arg2));
        }
    }

    /**
     * @Then I will see the data :arg1 with value :arg2
     */
    public function iWillSeeTheDataWithValue($arg1, $arg2)
    {
        $data = $this->getJsonContent();
        if (!isset($data[$arg1])) {
            throw new \Exception(sprintf('The key "%s" doesn\'t exist', $arg1));
        }

        if ($data[$arg1] != $arg2) {
            throw new \Exception("Expected '%s' but got '%s'", $arg3, $data[$arg1]);
        }
    }
}
