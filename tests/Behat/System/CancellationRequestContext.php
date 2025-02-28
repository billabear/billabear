<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\System;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\CancellationRequest;
use BillaBear\Repository\Orm\CancellationRequestRepository;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\SubscriptionPlanRepository;
use BillaBear\Repository\Orm\SubscriptionRepository;
use BillaBear\Subscription\CancellationType;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use BillaBear\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use BillaBear\Tests\Behat\Subscriptions\SubscriptionTrait;

class CancellationRequestContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private CancellationRequestRepository $cancellationRequestRepository,
        private SubscriptionRepository $subscriptionRepository,
        private SubscriptionPlanRepository $planRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @Given there are cancellation requests:
     */
    public function thereAreCancellationRequests(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $subscription = $this->getSubscription($row['Customer'], $row['Subscription Plan']);

            $cancellationRequest = new CancellationRequest();
            $cancellationRequest->setState($row['State'] ?? 'started');
            $cancellationRequest->setWhen($row['When'] ?? 'instantly');
            $cancellationRequest->setRefundType($row['Refund Type'] ?? 'none');
            $cancellationRequest->setSubscription($subscription);
            $cancellationRequest->setCreatedAt(new \DateTime());
            $cancellationRequest->setOriginalValidUntil($subscription->getValidUntil());
            $cancellationRequest->setCancellationType(CancellationType::COMPANY_REQUEST);

            $this->cancellationRequestRepository->getEntityManager()->persist($cancellationRequest);
        }
        $this->cancellationRequestRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the cancellation requests list
     */
    public function iViewTheCancellationRequestsList()
    {
        $this->sendJsonRequest('GET', '/app/system/cancellation-request/list');
    }

    /**
     * @Then I will see a cancellation request for :arg1 in state :arg2
     */
    public function iWillSeeACancellationRequestForInState($customerEmail, $state)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $request) {
            if ($request['subscription']['customer']['email'] === $customerEmail && $request['state'] === $state) {
                return;
            }
        }

        throw new \Exception("Can't find request");
    }

    /**
     * @When I view the cancellation requests for :arg1 and :arg2
     */
    public function iViewTheCancellationRequestsFor($customerEmail, $planName)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);
        $cancellationRequest = $this->cancellationRequestRepository->findOneBy(['subscription' => $subscription]);

        $this->sendJsonRequest('GET', '/app/system/cancellation-request/'.$cancellationRequest->getId().'/view');
    }

    /**
     * @Then I will the information for the cancellation request for :arg1
     */
    public function iWillTheInformationForTheCancellationRequestFor($customerEmail)
    {
        $data = $this->getJsonContent();
        if ($data['cancellation_request']['subscription']['customer']['email'] !== $customerEmail) {
            throw new \Exception("Didn't find request");
        }
    }

    /**
     * @When I process the cancellation requests for :arg1 and :arg2
     */
    public function iProcessTheCancellationRequestsForAnd($customerEmail, $planName)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);
        $cancellationRequest = $this->cancellationRequestRepository->findOneBy(['subscription' => $subscription]);

        $this->sendJsonRequest('POST', '/app/system/cancellation-request/'.$cancellationRequest->getId().'/process');
    }

    /**
     * @Then the response data for the cancellation request will have the state as :arg1
     */
    public function theResponseDataForTheCancellationRequestWillHaveTheStateAs($state)
    {
        $data = $this->getJsonContent();

        if ($data['state'] != $state) {
            throw new \Exception(sprintf("The state doesn't match. Expected '%s' but got '%s'", $state, $data['state']));
        }
    }
}
