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

namespace App\Tests\Behat\Subscriptions;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\Orm\PaymentDetailsServiceRepository;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionPlanServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionServiceRepository;

class MainContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;
    use SubscriptionTrait;

    public function __construct(
        private Session $session,
        private SubscriptionServiceRepository $subscriptionRepository,
        private PriceServiceRepository $priceRepository,
        private SubscriptionPlanServiceRepository $planRepository,
        private CustomerRepository $customerRepository,
        private PaymentDetailsServiceRepository $paymentDetailsRepository
    ) {
    }

    /**
     * @When the following subscriptions exist:
     */
    public function theFollowingSubscriptionsExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $subscriptionPlan = $this->planRepository->findOneBy(['name' => $row['Subscription Plan']]);
            $customer = $this->getCustomerByEmail($row['Customer']);
            /** @var Price $price */
            $price = $this->priceRepository->findOneBy(['amount' => $row['Price Amount'], 'currency' => $row['Price Currency'], 'schedule' => $row['Price Schedule']]);
            $paymentDetails = $this->paymentDetailsRepository->findOneBy(['customer' => $customer]);

            if (!$paymentDetails instanceof PaymentDetails) {
                throw new \Exception('Customer had no payment details');
            }

            $paymentReference = $paymentDetails->getStoredPaymentReference();

            $subscription = new Subscription();
            $subscription->setStatus('active');
            $subscription->setPaymentSchedule($row['Price Schedule']);
            $subscription->setCustomer($customer);
            $subscription->setPrice($price);
            $subscription->setSubscriptionPlan($subscriptionPlan);
            $subscription->setCurrency($price->getCurrency());
            $subscription->setAmount($price->getAmount());
            $subscription->setMainExternalReference('sdasd');
            $subscription->setMainExternalReferenceDetailsUrl('sdasd');
            $subscription->setChildExternalReference('saddsa');
            $subscription->setCreatedAt(new \DateTime('now'));
            $subscription->setUpdatedAt(new \DateTime('now'));
            $subscription->setPaymentDetails($paymentDetails);
            $subscription->setValidUntil(new \DateTime('+1 '.$row['Price Schedule']));

            $this->subscriptionRepository->getEntityManager()->persist($subscription);
            $this->subscriptionRepository->getEntityManager()->flush();

            $payment = new Payment();
            $payment->addSubscription($subscription);
            $payment->setPaymentReference($paymentReference);
            $payment->setMoneyAmount($price->getAsMoney());
            $payment->setCustomer($customer);
            $payment->setRefunded(false);
            $payment->setCompleted(true);
            $payment->setChargedBack(true);
            $payment->setCreatedAt(new \DateTime('now'));
            $payment->setUpdatedAt(new \DateTime('now'));

            $payment->setProvider('test_dummy');

            $this->subscriptionRepository->getEntityManager()->persist($payment);
            $this->subscriptionRepository->getEntityManager()->flush();
        }
    }

    /**
     * @Then I will see a subscription for :arg1
     */
    public function iWillSeeASubscriptionFor($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['subscriptions'])) {
            throw new \Exception('No subscriptions found');
        }

        foreach ($data['subscriptions'] as $subscription) {
            if ($subscription['plan']['name'] === $arg1) {
                return;
            }
        }

        throw new \Exception('No subscription found');
    }

    /**
     * @When I view the subscription list
     */
    public function iViewTheSubscriptionList()
    {
        $this->sendJsonRequest('GET', '/app/subscription');
    }

    /**
     * @Then I will see a subscription in the list for :arg1
     */
    public function iWillSeeASubscriptionInTheListFor($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['data'])) {
            throw new \Exception('No subscriptions found');
        }

        foreach ($data['data'] as $subscription) {
            if ($subscription['plan']['name'] === $arg1) {
                return;
            }
        }

        throw new \Exception('No subscription found');
    }

    /**
     * @When I view the subscription :arg1 for :arg2
     */
    public function iViewTheSubscriptionFor($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $this->sendJsonRequest('GET', '/app/subscription/'.(string) $subscription->getId());
    }

    /**
     * @When I update the subscription :arg1 for :arg2 to use the Payment method :arg3
     */
    public function iUpdateTheSubscriptionForToUseThePaymentMethod($planName, $customerEmail, $lastFour)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        /** @var PaymentDetails $paymentDetails */
        $paymentDetails = $this->paymentDetailsRepository->findOneBy(['lastFour' => $lastFour]);

        $this->sendJsonRequest('POST', '/app/subscription/'.(string) $subscription->getId().'/payment-method', ['payment_details' => (string) $paymentDetails->getId()]);
    }

    /**
     * @Then the subscription :arg1 for :arg2 will have the Payment Method :arg3
     */
    public function theSubscriptionForWillHaveThePaymentMethod($planName, $customerEmail, $lastFour)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        if ($subscription->getPaymentDetails()?->getLastFour() != $lastFour) {
            throw new \Exception('Got different payment details');
        }
    }

    /**
     * @Then the subscription :arg1 for :arg2 will be cancelled
     */
    public function theSubscriptionForWillBeCancelled($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        if ('cancelled' !== $subscription->getStatus()) {
            echo $this->session->getPage()->getContent();
            throw new \Exception('Not cancelled');
        }
    }

    /**
     * @Then I will see the subscription has the plan :arg1
     */
    public function iWillSeeTheSubscriptionHasThePlan($planName)
    {
        $data = $this->getJsonContent();

        if (!isset($data['subscription'])) {
            throw new \Exception('No subscription data');
        }
        if (!isset($data['subscription']['plan'])) {
            throw new \Exception('No subscription plan data');
        }
        if ($data['subscription']['plan']['name'] !== $planName) {
            throw new \Exception("Name doesn't match");
        }
    }

    /**
     * @Then I will see the subscription has the schedule :arg1
     */
    public function iWillSeeTheSubscriptionHasTheSchedule($schedule)
    {
        $data = $this->getJsonContent();

        if (!isset($data['subscription'])) {
            throw new \Exception('No subscription data');
        }
        if ($data['subscription']['schedule'] !== $schedule) {
            throw new \Exception("Schedule doesn't match");
        }
    }
}
