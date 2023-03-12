<?php

namespace App\Tests\Behat\Api;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Symfony\Component\HttpFoundation\Response;

class MainContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session)
    {
    }

    /**
     * @Then I should be told there is a conflict
     */
    public function iShouldBeToldThereIsAConflict()
    {
        if ($this->session->getStatusCode() !== Response::HTTP_CONFLICT) {
            throw new \Exception("No conflict response given");
        }
    }

    /**
     * @Given I have authenticated to the API
     */
    public function iHaveAuthenticatedToTheApi()
    {
    }

    /**
     * @Then there should be an error for :arg1
     */
    public function thereShouldBeAnErrorFor($errorKey)
    {
        $data = $this->getJsonContent();

        if (!isset($data['errors'])) {
            throw new \Exception('No errors');
        }

        if (!isset($data['errors'][$errorKey])) {
            throw new \Exception('No error');
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
            throw new \Exception("Found values in the data field");
        }
    }


    /**
     * @Then I should see in the API response with only :arg1 result in the data set
     */
    public function iShouldSeeInTheApiResponseWithOnlyResultInTheDataSet($arg1)
    {
        $data = $this->getJsonContent();

        if (count($data['data']) != intval($arg1)) {
            throw new \Exception(sprintf("Found %d results instead of %d",count($data['data']), $arg1));}
    }

    /**
     * @Then the I should see in the API response there are more results
     */
    public function theIShouldSeeInTheApiResponseThereAreMoreResults()
    {
        $data = $this->getJsonContent();

        if (!$data['has_more']) {
            throw new \Exception("API Response does not say there are more");
        }
    }


    /**
     * @Then the I should not see in the API response there are more results
     */
    public function theIShouldNotSeeInTheApiResponseThereAreMoreResults()
    {
        $data = $this->getJsonContent();
        if ($data['has_more']) {
            throw new \Exception("API Response does say there are more");
        }
    }


}
