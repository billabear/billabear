<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Subscriptions;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Behat\Step\Given;
use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\CustomerSubscriptionEvent;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\CustomerSubscriptionEventRepository;
use BillaBear\Repository\Orm\SubscriptionPlanRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use Parthenon\Billing\Enum\SubscriptionStatus;

class EventsContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private CustomerSubscriptionEventRepository $customerSubscriptionEventRepository,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
    ) {
    }

    /**
     * @Then there should be a churn event for :arg1
     */
    public function thereShouldBeAChurnEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::CHURNED, $arg1);
    }

    /**
     * @Then there should be an add on removed event for :arg1
     */
    public function thereShouldBeAnAddOnRemovedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::ADDON_REMOVED, $arg1);
    }

    /**
     * @Then there should be an activated event for :arg1
     */
    public function thereShouldBeAnActivatedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::ACTIVATED, $arg1);
    }

    /**
     * @Then there should be a trial ended event for :arg1
     */
    public function thereShouldBeATrialEndedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::TRIAL_ENDED, $arg1);
    }

    /**
     * @Then there should be a trial converted event for :arg1
     */
    public function thereShouldBeATrialExtendedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::TRIAL_CONVERTED, $arg1);
    }

    /**
     * @Then there should be a trial started event for :arg1
     */
    public function thereShouldBeATrialCreatedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::TRIAL_STARTED, $arg1);
    }

    /**
     * @Then there should not be a trial started event for :arg1
     */
    public function thereShouldNotBeATrialCreatedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::TRIAL_STARTED, $arg1, false);
    }

    /**
     * @Then there should be an add-on added event for :arg1
     */
    public function thereShouldBeAnAddOnAddedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::ADDON_ADDED, $arg1);
    }

    /**
     * @Then there should be a reactivated event for :arg1
     */
    public function thereShouldBeAReactivatedEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::REACTIVATED, $arg1);
    }

    /**
     * @Then there should be an downgrade event for :arg1
     */
    public function thereShouldBeAnDowngradeEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::DOWNGRADED, $arg1);
    }

    /**
     * @Then there should be an upgrade event for :arg1
     */
    public function thereShouldBeAnUpgradeEventFor($arg1)
    {
        $this->checkEventExists(CustomerSubscriptionEventType::UPGRADED, $arg1);
    }

    #[Given('there is not a trial started event for :arg1 on subscription plan :arg2')]
    public function thereIsNotATrialStartedEventForOnSubscriptionPlan(string $customerEmail, string $planName): void
    {
        $this->checkEventExistsForPlan(CustomerSubscriptionEventType::TRIAL_STARTED, $customerEmail, $planName, false);
    }

    #[Given('there is a trial started event for :arg1 on subscription plan :arg2')]
    public function thereIsATrialStartedEventForOnSubscriptionPlan(string $customerEmail, string $planName): void
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $plan = $this->subscriptionPlanRepository->findOneBy(['name' => $planName]);

        if (!$plan) {
            throw new \Exception(sprintf('Subscription plan "%s" not found', $planName));
        }

        $events = $this->customerSubscriptionEventRepository->findBy([
            'customer' => $customer,
            'eventType' => CustomerSubscriptionEventType::TRIAL_STARTED,
        ]);

        $found = false;
        foreach ($events as $event) {
            $subscription = $event->getSubscription();
            if ($subscription->getSubscriptionPlan()->getId() === $plan->getId()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Create a subscription for the customer on the specified plan
            $subscription = new \BillaBear\Entity\Subscription();
            $subscription->setCustomer($customer);
            $subscription->setSubscriptionPlan($plan);
            $subscription->setStatus(SubscriptionStatus::TRIAL_ACTIVE);
            $subscription->setStartOfCurrentPeriod(new \DateTime());
            $subscription->setValidUntil(new \DateTime('+30 days'));
            $subscription->setCreatedAt(new \DateTime());
            $subscription->setHasTrial(true);
            $subscription->setTrialLengthDays(30);

            $entityManager = $this->customerSubscriptionEventRepository->getEntityManager();
            $entityManager->persist($subscription);
            $entityManager->flush();

            // Create a trial started event
            $event = new CustomerSubscriptionEvent();
            $event->setCustomer($customer);
            $event->setSubscription($subscription);
            $event->setEventType(CustomerSubscriptionEventType::TRIAL_STARTED);
            $event->setCreatedAt(new \DateTime());

            $entityManager->persist($event);
            $entityManager->flush();
        }
    }

    private function checkEventExists(CustomerSubscriptionEventType $eventType, string $customerEmail, bool $find = true)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $event = $this->customerSubscriptionEventRepository->findOneBy(['customer' => $customer, 'eventType' => $eventType]);

        if (!$event instanceof CustomerSubscriptionEvent && $find) {
            throw new \Exception('Event was not found');
        } elseif ($event instanceof CustomerSubscriptionEvent && !$find) {
            throw new \Exception('Event was found');
        }
    }

    private function checkEventExistsForPlan(CustomerSubscriptionEventType $eventType, string $customerEmail, string $planName, bool $find = true)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $plan = $this->subscriptionPlanRepository->findOneBy(['name' => $planName]);

        if (!$plan) {
            throw new \Exception(sprintf('Subscription plan "%s" not found', $planName));
        }

        $events = $this->customerSubscriptionEventRepository->findBy(['customer' => $customer, 'eventType' => $eventType]);

        $found = false;
        foreach ($events as $event) {
            $subscription = $event->getSubscription();
            if ($subscription->getSubscriptionPlan()->getId() === $plan->getId()) {
                $found = true;
                break;
            }
        }

        if (!$found && $find) {
            throw new \Exception(sprintf('Event was not found for customer "%s" on plan "%s"', $customerEmail, $planName));
        } elseif ($found && !$find) {
            throw new \Exception(sprintf('Event was found for customer "%s" on plan "%s"', $customerEmail, $planName));
        }
    }
}
