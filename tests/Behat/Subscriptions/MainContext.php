<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Subscriptions;

use App\Dto\Request\App\Subscription\UpdatePlan;
use App\Entity\Subscription;
use App\Entity\SubscriptionPlan;
use App\Entity\SubscriptionSeatModification;
use App\Enum\SubscriptionSeatModificationType;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\SubscriptionRepository;
use App\Repository\Orm\SubscriptionSeatModificationRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

use function Symfony\Component\String\s;

class MainContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;
    use SubscriptionTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private SubscriptionRepository $subscriptionRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
        private CustomerRepository $customerRepository,
        private PaymentCardServiceRepository $paymentDetailsRepository,
        private SubscriptionSeatModificationRepository $subscriptionSeatModificationRepository,
    ) {
    }

    /**
     * @Then there will be :arg2 subscriptions on :arg1
     */
    public function thereWillBeSubscriptionsOn($count, $planName)
    {
        $subscriptionPlan = $this->findSubscriptionPlanByName($planName);

        $actualCount = $this->subscriptionRepository->count(['subscriptionPlan' => $subscriptionPlan]);

        if (intval($count) !== $actualCount) {
            throw new \Exception(sprintf('Expected %d but got %d', $count, $actualCount));
        }
    }

    /**
     * @Then there will be :arg3 subscriptions with the price :arg4 :arg1 per :arg2
     */
    public function thereWillBeSubscriptionsWithThePricePer($count, $amount, $currency, $schedule)
    {
        $price = $this->priceRepository->findOneBy(['amount' => $amount, 'currency' => $currency, 'schedule' => $schedule]);

        $actualCount = $this->subscriptionRepository->count(['price' => $price]);

        if (intval($count) !== $actualCount) {
            throw new \Exception(sprintf('Expected %d but got %d', $count, $actualCount));
        }
    }

    /**
     * @When I create a subscription via the site for :arg1 with the follow:
     */
    public function iCreateASubscriptionViaTheSiteForWithTheFollow($customerEmail, TableNode $table)
    {
        $row = current($table->getColumnsHash());
        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $row['Subscription Plan']]);
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['amount' => $row['Price Amount'], 'currency' => $row['Price Currency'], 'schedule' => $row['Price Schedule']]);
        $payload = [
            'subscription_plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ];
        if ('card' === $customer->getBillingType()) {
            $paymentCard = $this->paymentDetailsRepository->findOneBy(['customer' => $customer]);
            $payload['payment_details'] = (string) $paymentCard->getId();
        }

        if (isset($row['Seats'])) {
            $payload['seat_number'] = (int) $row['Seats'];
        }

        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/subscription', $payload);
    }

    /**
     * @Then the external references for the subscription for the user :arg1 should be blank
     */
    public function theExternalReferencesForTheSubscriptionForTheUserShouldBeBlank($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $subscription = $this->subscriptionRepository->findOneBy(['customer' => $customer]);

        if (!$subscription instanceof Subscription) {
            throw new \Exception('No subscription found');
        }

        if ($subscription->getMainExternalReference() || $subscription->getChildExternalReference()) {
            throw new \Exception('Expected no references');
        }
    }

    /**
     * @Then there should be a subscription for the user :arg1
     */
    public function thereShouldBeASubscriptionForTheUser($customerEmail)
    {
        $this->getSubscription($customerEmail);
    }

    /**
     * @When I add :arg2 seat to the suscription for :arg1
     */
    public function iAddSeatToTheSuscriptionFor($seatNumber, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        $this->sendJsonRequest('POST', '/api/v1/subscription/'.$subscription->getId().'/seats/add', ['seats' => intval($seatNumber)]);
    }

    /**
     * @When I set the seat number to :arg2 for the suscription for :arg1 in the APP
     */
    public function iSetTheSeatNumberToForTheSuscriptionForInTheApp($seatNumber, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        $this->sendJsonRequest('POST', '/app/subscription/'.$subscription->getId().'/seats/set', ['seats' => intval($seatNumber)]);
    }

    /**
     * @When I remove :arg2 seat to the suscription for :arg1
     */
    public function iRemoveSeatToTheSuscriptionFor($seatNumber, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        $this->sendJsonRequest('POST', '/api/v1/subscription/'.$subscription->getId().'/seats/remove', ['seats' => intval($seatNumber)]);
    }

    /**
     * @Then there is a subscription modification to remove :arg2 seats to the subscription for :arg1
     */
    public function thereIsASubscriptionModificationToRemoveSeatsToTheSubscriptionFor($seatNumber, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        $modification = $this->subscriptionSeatModificationRepository->findOneBy(['subscription' => $subscription, 'type' => SubscriptionSeatModificationType::REMOVED]);

        if (!$modification instanceof SubscriptionSeatModification) {
            throw new \Exception('Not found change');
        }

        if ($modification->getChangeValue() !== intval($seatNumber)) {
            throw new \Exception(sprintf('Expected %d but got %d', $seatNumber, $modification->getChangeValue()));
        }
    }

    /**
     * @Then there is a subscription modification to add :arg2 seats to the subscription for :arg1
     */
    public function thereIsASubscriptionModificationToAddSeatsToTheSubscriptionFor($seatNumber, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        $modification = $this->subscriptionSeatModificationRepository->findOneBy(['subscription' => $subscription, 'type' => SubscriptionSeatModificationType::ADDED]);

        if (!$modification instanceof SubscriptionSeatModification) {
            throw new \Exception('Not found change');
        }

        if ($modification->getChangeValue() !== intval($seatNumber)) {
            throw new \Exception(sprintf('Expected %d but got %d', $seatNumber, $modification->getChangeValue()));
        }
    }

    /**
     * @Then the subscription for :arg1 has :arg2 seats
     */
    public function theSubscriptionForHasSeats($customerEmail, $seatNumber)
    {
        $subscription = $this->getSubscription($customerEmail);

        if ($subscription->getSeats() !== intval($seatNumber)) {
            var_dump($this->getJsonContent());
            throw new \Exception(sprintf('Expected %d but got %d', $seatNumber, $subscription->getSeats()));
        }
    }

    /**
     * @Then there should not be a subscription for the user :arg1
     */
    public function thereShouldNotBeASubscriptionForTheUser($customerEmail)
    {
        try {
            $this->getSubscription($customerEmail);
        } catch (\Exception $e) {
            return;
        }
        throw new \Exception('Found Subscription');
    }

    /**
     * @Then the response should be that payment is needed
     */
    public function theResponseShouldBeThatPaymentIsNeeded()
    {
        if (402 !== $this->session->getStatusCode()) {
            throw new \Exception('Got '.$this->session->getStatusCode());
        }
    }

    /**
     * @Then the subscription for :arg1 will expire in a week
     */
    public function theSubscriptionForWillExpireInAWeek($customerEmail)
    {
        $now = new \DateTime();
        $subscription = $this->getSubscription($customerEmail);

        $diff = $subscription->getValidUntil()->diff($now);

        if ($diff->d < 6) {
            throw new \Exception('Expires in less than a week');
        }
        if ($diff->d > 7) {
            throw new \Exception('Expires in more than a week');
        }
    }

    /**
     * @Then the subscription for :arg1 will expire in a month
     */
    public function theSubscriptionForWillExpireInAMonth($customerEmail)
    {
        $now = new \DateTime();
        $subscription = $this->getSubscription($customerEmail);

        $diff = $subscription->getValidUntil()->diff($now);
        // To handle it's the 31 and next month is billed on the 30th.
        // Or it's January 31 and it's billed on the
        if (
            0 == $diff->m

                && $diff->d < (int) $subscription->getValidUntil()->format('t')
        ) {
            throw new \Exception(sprintf('Invoice expires in %d days and %d months', $diff->d, $diff->m));
        }
    }

    /**
     * @Then the subscription for :arg1 will expire in a year
     */
    public function theSubscriptionForWillExpireInAYear($customerEmail)
    {
        $now = new \DateTime();
        $subscription = $this->getSubscription($customerEmail);

        $diff = $subscription->getValidUntil()->diff($now);
        if (1 != $diff->y) {
            var_dump($diff);
            throw new \Exception('Expires another time');
        }
    }

    /**
     * @Then the subscription for :arg1 will expire today
     */
    public function theSubscriptionForWillExpireToday($customerEmail)
    {
        $now = new \DateTime();
        $subscription = $this->getSubscription($customerEmail);

        $diff = $subscription->getValidUntil()->diff($now);

        if (0 !== $diff->d || 0 !== $diff->m || 0 !== $diff->y) {
            throw new \Exception('Expires a different day');
        }
    }

    /**
     * @When the following subscriptions exist:
     */
    public function theFollowingSubscriptionsExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            /** @var SubscriptionPlan $subscriptionPlan */
            $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $row['Subscription Plan']]);
            $customer = $this->getCustomerByEmail($row['Customer']);
            /** @var Price $price */
            $price = $this->priceRepository->findOneBy(['amount' => $row['Price Amount'], 'currency' => $row['Price Currency'], 'schedule' => $row['Price Schedule']]);
            $paymentDetails = $this->paymentDetailsRepository->findOneBy(['customer' => $customer]);

            if (!$paymentDetails instanceof PaymentCard) {
                throw new \Exception('Customer had no payment details');
            }

            $paymentReference = $paymentDetails->getStoredPaymentReference();

            $subscription = new Subscription();
            $subscription->setPaymentSchedule($row['Price Schedule']);
            $subscription->setCustomer($customer);
            $subscription->setPrice($price);
            $subscription->setSubscriptionPlan($subscriptionPlan);
            $subscription->setPlanName($subscriptionPlan->getName());
            $subscription->setStatus('Active' === ($row['Status'] ?? 'Active') ? SubscriptionStatus::ACTIVE : SubscriptionStatus::CANCELLED);
            $subscription->setCurrency($price->getCurrency());
            $subscription->setAmount($price->getAmount());
            $subscription->setMainExternalReference('sdasd');
            $subscription->setMainExternalReferenceDetailsUrl('sdasd');
            $subscription->setChildExternalReference('saddsa');
            $subscription->setCreatedAt(new \DateTime($row['Started At'] ?? 'now'));
            $subscription->setUpdatedAt(new \DateTime('now'));
            $subscription->setStartOfCurrentPeriod(new \DateTime($row['Started Current Period'] ?? 'now'));
            $subscription->setPaymentDetails($paymentDetails);
            $subscription->setValidUntil(new \DateTime($row['Next Charge'] ?? '+1 '.$row['Price Schedule']));

            if (isset($row['Ended At'])) {
                $subscription->setEndedAt(new \DateTime($row['Ended At']));
            }

            if (isset($row['Seats'])) {
                $subscription->setSeats(intval($row['Seats']));
            }

            $this->subscriptionRepository->getEntityManager()->persist($subscription);
            $this->subscriptionRepository->getEntityManager()->flush();

            $payment = new Payment();
            $payment->addSubscription($subscription);
            $payment->setPaymentReference($paymentReference);
            $payment->setMoneyAmount($price->getAsMoney());
            $payment->setCustomer($customer);
            $payment->setStatus(PaymentStatus::COMPLETED);
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
     * @Then I will see the payments for the subscription
     */
    public function iWillSeeThePaymentsForTheSubscription()
    {
        $data = $this->getJsonContent();

        if (!isset($data['payments']) || empty($data['payments'])) {
            throw new \Exception('No payments');
        }
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
     * @Then I will not see a subscription in the list for :arg1
     */
    public function iWillNotSeeASubscriptionInTheListFor($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['data'])) {
            throw new \Exception('No subscriptions found');
        }

        foreach ($data['data'] as $subscription) {
            if ($subscription['plan']['name'] === $arg1) {
                throw new \Exception('subscription found');
            }
        }
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

        $this->sendJsonRequest('POST', '/app/subscription/'.(string) $subscription->getId().'/payment-card', ['payment_details' => (string) $paymentDetails->getId()]);
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
     * @When I make a API Request to update the subscription :arg1 for :arg2 to plan:
     */
    public function iMakeAApiRequestToUpdateTheSubscriptionForToPlan($planName, $customerEmail, TableNode $table)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $data = $table->getRowsHash();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $data['Plan']]);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $subscriptionPlan->getProduct(), 'amount' => $data['Price'], 'currency' => $data['Currency']]);

        $this->sendJsonRequest('POST', '/api/v1/subscription/'.$subscription->getId().'/plan', [
            'when' => UpdatePlan::NEXT_CYCLE,
            'plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ]);
    }

    /**
     * @When I make a API Request to update the subscription :arg1 for :arg2 to plan to be changed instantly:
     */
    public function iMakeAApiRequestToUpdateTheSubscriptionForToPlanToBeChangedInstantly($planName, $customerEmail, TableNode $table)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $data = $table->getRowsHash();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $data['Plan']]);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $subscriptionPlan->getProduct(), 'amount' => $data['Price'], 'currency' => $data['Currency']]);

        $this->sendJsonRequest('POST', '/api/v1/subscription/'.$subscription->getId().'/plan', [
            'when' => UpdatePlan::WHEN_INSTANTLY,
            'plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ]);
    }

    /**
     * @When I update the subscription :arg1 for :arg2 to plan:
     */
    public function iUpdateTheSubscriptionForToPlan($planName, $customerEmail, TableNode $table)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $data = $table->getRowsHash();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $data['Plan']]);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $subscriptionPlan->getProduct(), 'amount' => $data['Price'], 'currency' => $data['Currency']]);

        $this->sendJsonRequest('POST', '/app/subscription/'.$subscription->getId().'/change-plan', [
            'when' => UpdatePlan::NEXT_CYCLE,
            'plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ]);
    }

    /**
     * @When I update the subscription :arg1 for :arg2 to plan to be changed instantly:
     */
    public function iUpdateTheSubscriptionForToPlanToBeChangedInstantly($planName, $customerEmail, TableNode $table)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $data = $table->getRowsHash();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $data['Plan']]);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $subscriptionPlan->getProduct(), 'amount' => $data['Price'], 'currency' => $data['Currency']]);

        $this->sendJsonRequest('POST', '/app/subscription/'.$subscription->getId().'/change-plan', [
            'when' => UpdatePlan::WHEN_INSTANTLY,
            'plan' => (string) $subscriptionPlan->getId(),
            'price' => (string) $price->getId(),
        ]);
    }

    /**
     * @Then the subscription :arg1 for :arg2 will not exist
     */
    public function theSubscriptionForWillNotExist($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);
        if ($subscription->getPlanName() === $planName) {
            var_dump($this->getJsonContent());
            throw new \Exception('Plan is '.$subscription->getPlanName());
        }
    }

    /**
     * @Then the subscription :arg1 for :arg2 will exist
     */
    public function theSubscriptionForWillExist($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail);

        if ($subscription->getPlanName() !== $planName) {
            throw new \Exception('Plan is '.$subscription->getPlanName());
        }
    }

    /**
     * @When I go to update the subscription plan for :arg1 for :arg2
     */
    public function iGoToUpdateTheSubscriptionPlanForFor($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        $this->sendJsonRequest('GET', '/app/subscription/'.$subscription->getId().'/change-plan');
    }

    /**
     * @Then I will see the plan :arg1 with the price :arg3 in :arg2
     */
    public function iWillSeeThePlanWithThePriceIn($planName, $amount, $currency)
    {
        $data = $this->getJsonContent();

        foreach ($data['plans'] as $plan) {
            if ($plan['name'] === $planName) {
                foreach ($plan['prices'] as $price) {
                    if ($price['amount'] == $amount && $currency === $price['currency']) {
                        return;
                    }
                }
            }
        }
        throw new \Exception('Unable to see plan and price');
    }

    /**
     * @Then I will not see the plan :arg1 with the price :arg3 in :arg2
     */
    public function iWillNotSeeThePlanWithThePriceIn($planName, $amount, $currency)
    {
        $data = $this->getJsonContent();

        foreach ($data['plans'] as $plan) {
            if ($plan['name'] === $planName) {
                foreach ($plan['prices'] as $price) {
                    if ($price['amount'] == $amount && $currency === $price['currency']) {
                        throw new \Exception('Can to see plan and price');
                    }
                }
            }
        }
    }

    /**
     * @When I update the subscription :planName for :customerEmail to use the :priceAmount in :currency per :schedule price
     */
    public function iUpdateTheSubscriptionForToUseTheInPerPrice($planName, $customerEmail, $priceAmount, $currency, $schedule)
    {
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['amount' => $priceAmount, 'currency' => $currency, 'schedule' => $schedule]);

        $subscription = $this->getSubscription($customerEmail, $planName);
        $this->sendJsonRequest('POST', '/app/subscription/'.$subscription->getId().'/price', ['price' => (string) $price->getId()]);
    }

    /**
     * @Then the subscription :arg1 for :arg2 will be for :arg5 in :arg3 per :arg4
     */
    public function theSubscriptionForWillBeForInPer($planName, $customerEmail, $priceAmount, $currency, $schedule)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['amount' => $priceAmount, 'currency' => $currency, 'schedule' => $schedule]);

        $subscription = $this->getSubscription($customerEmail, $planName);

        if ($subscription->getPrice()->getId() != $price->getId()) {
            throw new \Exception('Price not the same');
        }
    }

    /**
     * @Then the subscription :arg1 for :arg2 will be cancelled
     */
    public function theSubscriptionForWillBeCancelled($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        if (SubscriptionStatus::CANCELLED !== $subscription->getStatus()) {
            var_dump($this->session->getPage()->getContent());
            throw new \Exception('Not cancelled');
        }
    }

    /**
     * @Then the subscription :arg1 for :arg2 will be pending cancel
     */
    public function theSubscriptionForWillBePendingCancel($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        if (SubscriptionStatus::PENDING_CANCEL !== $subscription->getStatus()) {
            throw new \Exception('Not cancelled');
        }
    }

    /**
     * @Then the subscription :arg1 for :arg2 will not be cancelled
     */
    public function theSubscriptionForWillNotBeCancelled($planName, $customerEmail)
    {
        $subscription = $this->getSubscription($customerEmail, $planName);

        if (SubscriptionStatus::CANCELLED === $subscription->getStatus()) {
            throw new \Exception('Cancelled');
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

    /**
     * @throws \Exception
     */
    public function getSubscription($customerEmail): Subscription
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $subscription = $this->subscriptionRepository->findOneBy(['customer' => $customer]);

        if (!$subscription instanceof Subscription) {
            throw new \Exception('No subscription found');
        }

        $this->subscriptionRepository->getEntityManager()->refresh($subscription);

        return $subscription;
    }
}
