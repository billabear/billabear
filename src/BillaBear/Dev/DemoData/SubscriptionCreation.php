<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dev\DemoData;

use BillaBear\Command\DevDemoDataCommand;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use Faker\Factory;
use Parthenon\Athena\Filters\GreaterThanFilter;
use Parthenon\Billing\Entity\PaymentCard;
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
        private SubscriptionCreationProcessor $subscriptionCreationProcessor,
    ) {
    }

    public function createData(OutputInterface $output, bool $writeToStripe): void
    {
        $output->writeln("\nCreate Subscriptions");
        $faker = Factory::create();
        $subscriptionPlans = $this->subscriptionPlanRepository->getList(limit: 1000)->getResults();

        $totalCount = DevDemoDataCommand::getNumberOfCustomers();

        $origStartDate = clone DevDemoDataCommand::getStartDate();
        $origStartDate->modify('first day of this month');
        $now = new \DateTime('now');
        $interval = $origStartDate->diff($now);

        $numberOfMonths = abs($interval->m);

        $elements = [];
        $currentValue = intval($totalCount / $numberOfMonths);

        for ($i = 0; $i < $numberOfMonths; ++$i) {
            $elements[] = $currentValue;
            $currentValue -= intval($currentValue * 0.25);
        }

        for ($i = 0; $i < $numberOfMonths; ++$i) {
            $elements[$i] = intval($elements[$i] + (($totalCount - array_sum($elements)) * 0.50));
            if (array_sum($elements) >= $totalCount) {
                break;
            }
        }
        ++$elements[0];
        $limit = 25;
        $lastId = null;
        $progressBar = new ProgressBar($output, $totalCount);

        $progressBar->start();
        foreach ($elements as $step) {
            $a = 0;
            while ($a < $step) {
                $filter = new GreaterThanFilter();
                $filter->setFieldName('createdAt');
                $filter->setData($origStartDate);

                $filters = []; // [$filter];
                $customers = $this->customerRepository->getList(filters: $filters, limit: $limit, lastId: $lastId);
                $lastId = $customers->getLastKey();

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
                    $subscription->setPlanName($subscriptionPlan->getName());
                    $subscription->setPaymentSchedule($price->getSchedule());
                    $subscription->setAmount($price->getAmount());
                    $subscription->setCurrency($price->getCurrency());
                    if ($card instanceof PaymentCard) {
                        $subscription->setPaymentDetails($card);
                    }
                    $startDate = clone $origStartDate;
                    $startDate->modify('first day of this month');
                    $numberOfDaysInMonth = (int) $startDate->format('t');
                    $days = $faker->numberBetween(1, $numberOfDaysInMonth) - 1;
                    $startDate->modify('+'.$days.' days');
                    $subscription->setCreatedAt($startDate);
                    $subscription->setStartOfCurrentPeriod($startDate);
                    $subscription->setUpdatedAt(new \DateTime('now'));
                    $subscription->setValidUntil($startDate);

                    $this->subscriptionRepository->save($subscription);

                    $process = new \BillaBear\Entity\SubscriptionCreation();
                    $process->setSubscription($subscription);
                    $process->setCreatedAt($startDate);
                    $process->setState('started');
                    $this->subscriptionCreationProcessor->process($process);
                }
                if ($totalCount <= $progressBar->getProgress()) {
                    break 2;
                }
            }
            $origStartDate->modify('+1 month');
        }
        $progressBar->finish();
    }
}
