<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Doctrine\ORM\EntityManagerInterface;
use Parthenon\Payments\Plan\PlanManager;

class PlanContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private PlanManager $planManager)
    {
    }

    /**
     * @When I view the plans
     */
    public function iViewThePlans()
    {
        $this->sendJsonRequest('GET', '/api/plans');
    }

    /**
     * @Then I should see the plans that are configured
     */
    public function iShouldSeeThePlansThatAreConfigured()
    {
        $content = $this->getJsonContent();

        $plans = $this->planManager->getPlans();

        foreach ($plans as $plan)
        {
            $name = $plan->getName();
            if (!isset($content['plans'][$name])) {
                throw new \Exception("Can't see plan ". $name);
            }

            if ($content['plans'][$name]['limits'] != $plan->getLimits()) {
                throw new \Exception("Plan for ". $plan->getLimits());
            }
        }
    }
}