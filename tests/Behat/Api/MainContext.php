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
}
