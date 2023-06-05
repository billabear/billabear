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

namespace App\Dev\DemoData;

use App\Command\DevDemoDataCommand;
use App\Entity\Customer;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Stats\SubscriptionCreationStats;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionCreation
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private SubscriptionCreationStats $subscriptionCreationStats,
    ) {
    }

    public function createData(OutputInterface $output): void
    {
        $output->writeln('Create Subscriptions');
        $faker = \Faker\Factory::create();
        $subscriptionPlans = $this->subscriptionPlanRepository->getList(limit: 1000)->getResults();

        $limit = 25;
        $count = 0;

        $step = 10;
        $percentage = 25;
        $lastId = null;
        $progressBar = new ProgressBar($output, DevDemoDataCommand::NUMBER_OF_CUSTOMERS);

        $progressBar->start();
        while ($count < DevDemoDataCommand::NUMBER_OF_CUSTOMERS) {
            $step += intval($step * ($percentage / 100));
            $a = 0;
            $pastMonths = 16;
            var_dump($step, DevDemoDataCommand::NUMBER_OF_CUSTOMERS);
            while ($a < $step) {
                $customers = $this->customerRepository->getList(limit: $limit, lastId: $lastId);
                $count += $limit;
                $lastId = $customers->getLastKey();
                --$pastMonths;
                /** @var Customer $customer */
                foreach ($customers->getResults() as $customer) {
                    ++$a;
                    $progressBar->advance();
                    $cards = $this->paymentCardRepository->getPaymentCardForCustomer($customer);

                    $card = current($cards);

                    /** @var SubscriptionPlan $subscriptionPlan */
                    $subscriptionPlan = $faker->randomElement($subscriptionPlans);
                    /** @var Price $price */
                    $price = $faker->randomElement($subscriptionPlan->getPrices()->toArray());
                    $subscription = new Subscription();
                    $subscription->setStatus(SubscriptionStatus::ACTIVE);
                    $subscription->setSubscriptionPlan($subscriptionPlan);
                    $subscription->setCustomer($customer);
                    $subscription->setPrice($price);
                    $subscription->setPaymentSchedule($price->getSchedule());
                    $subscription->setAmount($price->getAmount());
                    $subscription->setCurrency($price->getCurrency());
                    if ($card instanceof PaymentCard) {
                        $subscription->setPaymentDetails($card);
                    }
                    $startDate = new \DateTime('-'.$pastMonths.' month');
                    $startDate->modify('first day of this month');
                    $numberOfDaysInMonth = (int) $startDate->format('t');
                    $days = $faker->numberBetween(1, $numberOfDaysInMonth) - 1;
                    $startDate->modify('+'.$days.' days');
                    $subscription->setCreatedAt($startDate);
                    $subscription->setStartOfCurrentPeriod($startDate);
                    $subscription->setUpdatedAt(new \DateTime('now'));
                    $subscription->setValidUntil(new \DateTime('+1 '.$price->getSchedule()));

                    $this->subscriptionRepository->save($subscription);

                    $this->subscriptionCreationStats->handleStats($subscription);
                }
            }
        }
        $progressBar->finish();
    }
}
