<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Interopt\Stripe;

use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\SubscriptionRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class SubscriptionsContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private SubscriptionRepository $subscriptionRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planRepository,
    ) {
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayer()
    {
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions');
    }

    /**
     * @Then I will see a subscription in the stripe interopt list for :arg1
     */
    public function iWillSeeASubscriptionInTheStripeInteroptListFor($planName)
    {
        $data = $this->getJsonContent();

        if ('list' !== $data['object']) {
            throw new \Exception('Expected a list response');
        }

        foreach ($data['data'] as $subscription) {
            if ($subscription['metadata']['plan_name'] == $planName) {
                return;
            }
        }

        throw new \Exception('Could not find subscription');
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for customer :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCustomer($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?customer='.$customer->getId());
    }

    /**
     * @Then I will not see a subscription in the stripe interopt list for :arg1
     */
    public function iWillNotSeeASubscriptionInTheStripeInteroptListFor($planName)
    {
        $data = $this->getJsonContent();

        if ('list' !== $data['object']) {
            throw new \Exception('Expected a list response');
        }

        foreach ($data['data'] as $subscription) {
            if ($subscription['metadata']['plan_name'] == $planName) {
                throw new \Exception('Found subscription');
            }
        }
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for price :amount :currency :schedule
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForPrice($amount, $currency, $schedule)
    {
        $price = $this->priceRepository->findOneBy(['amount' => $amount, 'currency' => $currency, 'schedule' => $schedule]);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?price='.$price->getId());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for created at :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCreatedAt($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?created='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for created at greater than :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCreatedAtGreaterThan($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?created[gt]='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for created at less than :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCreatedAtLessThan($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?created[lt]='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for end of current period :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForEndOfCurrentPeriod($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?current_period_end='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for end of current period less than :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForEndOfCurrentPeriodLessThan($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?current_period_end[lt]='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for start of current period :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForStartOfCurrentPeriod($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?current_period_start='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for start of current period less than :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForStartOfCurrentPeriodLessThan($arg1)
    {
        $dateTime = new \DateTime($arg1);

        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?current_period_start[lt]='.$dateTime->getTimestamp());
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for collection type :arg1
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCollectionType($collectionMethod)
    {
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?collection_method='.$collectionMethod);
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for active subscriptions
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForActiveSubscriptions()
    {
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?status=active');
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer for cancelled subscriptions
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayerForCancelledSubscriptions()
    {
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions?status=canceled');
    }

    /**
     * @Then I will see :arg1 results in the stripe interopt list
     */
    public function iWillSeeResultsInTheStripeInteroptList($count)
    {
        $data = $this->getJsonContent();
        if (intval($count) !== count($data['data'])) {
            throw new \Exception(sprintf("Count didn't match. Got %d results", count($data['data'])));
        }
    }

    /**
     * @When I cancel via the stripe interopt api the subscription :arg1 for :arg2
     */
    public function iCancelViaTheStripeInteroptApiTheSubscriptionFor($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);
        $this->isStripe(true);
        $this->sendJsonRequest('DELETE', '/interopt/stripe/v1/subscriptions/'.$subscription->getId());
    }

    /**
     * @When I cancel via the stripe interopt api the subscription :arg1 for :arg2 with prorata cancellation
     */
    public function iCancelViaTheStripeInteroptApiTheSubscriptionForWithProrataCancellation($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);
        $this->isStripe(true);
        $this->sendJsonRequest('DELETE', '/interopt/stripe/v1/subscriptions/'.$subscription->getId(), ['prorate' => true]);
    }
}
