<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Subscriptions;

use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\SubscriptionRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;
    use SubscriptionTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private SubscriptionRepository $subscriptionRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planRepository,
        private CustomerRepository $customerRepository,
        private PaymentCardServiceRepository $paymentDetailsRepository,
    ) {
    }

    /**
     * @When I request the subscription list api
     */
    public function iRequestTheSubscriptionListApi()
    {
        $this->sendJsonRequest('GET', '/api/v1/subscription');
    }

    /**
     * @When I request the subscription list api for :arg1
     */
    public function iRequestTheSubscriptionListApiFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/subscription');
    }

    /**
     * @When I request via the API the subscription :arg1 for :arg2
     */
    public function iRequestViaTheApiTheSubscriptionFor($planName, $customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $subscriptionPlan = $this->planRepository->findOneBy(['name' => $planName]);

        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionPlan' => $subscriptionPlan, 'customer' => $customer]);

        if (!$subscription instanceof Subscription) {
            throw new \Exception("Can't find subscription");
        }

        $this->sendJsonRequest('GET', '/api/v1/subscription/'.(string) $subscription->getId());
    }

    /**
     * @Then I will see the REST subscription has the plan :arg1
     */
    public function iWillSeeTheRestSubscriptionHasThePlan($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['plan']['name'] !== $arg1) {
            throw new \Exception("Doesn't match");
        }
    }

    /**
     * @Then I will see the REST subscription has the schedule :arg1
     */
    public function iWillSeeTheRestSubscriptionHasTheSchedule($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['schedule'] !== $arg1) {
            throw new \Exception("Doesn't match");
        }
    }

    /**
     * @When I cancel via the API the subscription :arg1 for :arg2
     */
    public function iCancelViaTheApiTheSubscriptionFor($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $payload = [
            'when' => 'instantly',
            'refund_type' => 'none',
        ];
        $this->sendJsonRequest('POST', '/api/v1/subscription/'.$subscription->getId().'/cancel', $payload);
    }

    /**
     * @When I update the subscription :arg1 for :arg2 to use the Payment method :arg3 via API
     */
    public function iUpdateTheSubscriptionForToUseThePaymentMethodViaApi($planName, $customerEmail, $lastFour)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        /** @var PaymentCard $paymentDetails */
        $paymentDetails = $this->paymentDetailsRepository->findOneBy(['lastFour' => $lastFour]);

        $this->sendJsonRequest('PUT', '/api/v1/subscription/'.(string) $subscription->getId().'/payment-method', ['payment_details' => (string) $paymentDetails->getId()]);
    }

    /**
     * @When I create a subscription via the API for :arg1 with the follow:
     */
    public function iCreateASubscriptionViaTheApiForWithTheFollow($customerEmail, TableNode $table)
    {
        $row = current($table->getColumnsHash());
        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->planRepository->findOneBy(['name' => $row['Subscription Plan']]);
        $customer = $this->getCustomerByEmail($customerEmail);
        $priceCritera = ['amount' => $row['Price Amount'], 'currency' => $row['Price Currency']];
        if (isset($row['Price Schedule'])) {
            $priceCritera['schedule'] = $row['Price Schedule'];
        }
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy($priceCritera);

        $this->sendJsonRequest('POST', '/api/v1/customer/'.$customer->getId().'/subscription/start', [
            'subscription_plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ]);
    }

    /**
     * @When I create a subscription with code and currency via the API for :arg1 with the follow:
     */
    public function iCreateASubscriptionWithCodeAndCurrencyViaTheApiForWithTheFollow($customerEmail, TableNode $table)
    {
        $row = current($table->getColumnsHash());
        $customer = $this->getCustomerByEmail($customerEmail);

        $this->sendJsonRequest('POST', '/api/v1/customer/'.$customer->getId().'/subscription/start', [
            'subscription_plan' => (string) $row['Subscription Plan'],
            'currency' => $row['Price Currency'],
            'schedule' => $row['Price Schedule'],
        ]);
    }
}
