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
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionPlanServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionServiceRepository;

class MainContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SubscriptionServiceRepository $subscriptionRepository,
        private PriceServiceRepository $priceRepository,
        private SubscriptionPlanServiceRepository $planRepository,
        private CustomerRepository $customerRepository,
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
            $subscription->setValidUntil(new \DateTime('+1 '.$row['Price Schedule']));

            $this->subscriptionRepository->getEntityManager()->persist($subscription);
        }
        $this->subscriptionRepository->getEntityManager()->flush();
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
}
