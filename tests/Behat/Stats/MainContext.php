<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Stats;

use App\Entity\Customer;
use App\Entity\Stats\CachedStats;
use App\Entity\Stats\ChargeBackAmountDailyStats;
use App\Entity\Stats\ChargeBackAmountMonthlyStats;
use App\Entity\Stats\ChargeBackAmountYearlyStats;
use App\Entity\Stats\PaymentAmountDailyStats;
use App\Entity\Stats\PaymentAmountMonthlyStats;
use App\Entity\Stats\PaymentAmountYearlyStats;
use App\Entity\Stats\RefundAmountDailyStats;
use App\Entity\Stats\RefundAmountMonthlyStats;
use App\Entity\Stats\RefundAmountYearlyStats;
use App\Entity\Stats\SubscriptionCancellationDailyStats;
use App\Entity\Stats\SubscriptionCancellationMonthlyStats;
use App\Entity\Stats\SubscriptionCancellationYearlyStats;
use App\Entity\Stats\SubscriptionCreationDailyStats;
use App\Entity\Stats\SubscriptionCreationMonthlyStats;
use App\Entity\Stats\SubscriptionCreationYearlyStats;
use App\Repository\Orm\CachedStatsRepository;
use App\Repository\Orm\PaymentAmountDailyStatsRepository;
use App\Repository\Orm\RefundAmountDailyStatsRepository;
use App\Repository\Orm\SubscriptionCreationDailyStatsRepository;
use App\Repository\Orm\SubscriptionCreationMonthlyStatsRepository;
use App\Repository\Orm\SubscriptionCreationYearlyStatsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class MainContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SubscriptionCreationDailyStatsRepository $subscriptionDailyStatsRepository,
        private SubscriptionCreationMonthlyStatsRepository $subscriptionCreationMonthlyStatsRepository,
        private SubscriptionCreationYearlyStatsRepository $subscriptionCreationYearlyStatsRepository,
        private PaymentAmountDailyStatsRepository $paymentAmountDailyStatsRepository,
        private RefundAmountDailyStatsRepository $refundAmountDailyStatsRepository,
        private CachedStatsRepository $cachedStatsRepository,
    ) {
    }

    /**
     * @Then the subscriber daily stat for the day should be :arg1
     */
    public function theSubscriberDailyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
            ]);

        if (!$statEntity instanceof SubscriptionCreationDailyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the subscriber monthly stat for the day should be :arg1
     */
    public function theSubscriberWeeklyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionCreationMonthlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => 1,
        ]);

        if (!$statEntity instanceof SubscriptionCreationMonthlyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the subscriber yearly stat for the day should be :arg1
     */
    public function theSubscriberYearlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionCreationYearlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => 1,
            'day' => 1,
        ]);

        if (!$statEntity instanceof SubscriptionCreationYearlyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the payment amount stats for the day should be :arg2 in the currency :arg1
     */
    public function thePaymentAmountStatsForTheDayShouldBeInTheCurrency($amount, $currency)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->paymentAmountDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
        ]);

        if (!$statEntity instanceof PaymentAmountDailyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getAmount() != $amount) {
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $statEntity->getAmount()));
        }
        if ($statEntity->getCurrency() != $currency) {
            throw new \Exception('Currency is wrong');
        }
    }

    /**
     * @Then there will be a refund amount daily stat for :arg2 in the currency :arg1
     */
    public function thereWillBeARefundAmountDailyStatForInTheCurrency($amount, $currency)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->refundAmountDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
        ]);

        if (!$statEntity instanceof RefundAmountDailyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getAmount() != $amount) {
            throw new \Exception('Amount is wrong');
        }
        if ($statEntity->getCurrency() != $currency) {
            throw new \Exception('Currency is wrong');
        }
    }

    /**
     * @Given that there are stats for :arg1 years existing for default brand
     */
    public function thatThereAreStatsForYearsExistingForDefaultBrand($yearCount)
    {
        for ($i = $yearCount; $i >= 0; --$i) {
            $startDate = new \DateTime(sprintf('-%d years', $i));
            $startDate->modify('first day of january');
            $this->generateYearStats($startDate);
        }

        $startDate = new \DateTime('-13 months');
        $startDate->modify('first day of this month');
        $this->generateMonthlyStats($startDate);
        $endDate = new \DateTime('+1 day');
        while ($startDate <= $endDate) {
            $startDate->modify('+1 month');
            $this->generateMonthlyStats($startDate);
        }

        $startDate = new \DateTime('-13 months');
        $endDate = new \DateTime('+1 day');
        while ($startDate <= $endDate) {
            $this->generateDailyStats($startDate);
            $startDate->modify('+1 day');
        }
    }

    private function generateYearStats(\DateTime $dateTime)
    {
        $subscriptionCreationStat = new SubscriptionCreationYearlyStats();
        $subscriptionCreationStat->setYear((int) $dateTime->format('Y'));
        $subscriptionCreationStat->setMonth(1);
        $subscriptionCreationStat->setDay(1);
        $subscriptionCreationStat->setCount(random_int(1, 3000));
        $subscriptionCreationStat->setBrandCode(Customer::DEFAULT_BRAND);

        $subscriptionCancellation = new SubscriptionCancellationYearlyStats();
        $subscriptionCancellation->setYear((int) $dateTime->format('Y'));
        $subscriptionCancellation->setMonth(1);
        $subscriptionCancellation->setDay(1);
        $subscriptionCancellation->setCount(random_int(1, 3000));
        $subscriptionCancellation->setBrandCode(Customer::DEFAULT_BRAND);

        $paymentAmountStat = new PaymentAmountYearlyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth(1);
        $paymentAmountStat->setDay(1);
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('USD');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountYearlyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth(1);
        $refundAmount->setDay(1);
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('USD');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountYearlyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth(1);
        $chargeBackAmount->setDay(1);
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('USD');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCreationStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCancellation);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();

        $paymentAmountStat = new PaymentAmountYearlyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth(1);
        $paymentAmountStat->setDay(1);
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('EUR');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountYearlyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth(1);
        $refundAmount->setDay(1);
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('EUR');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountYearlyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth(1);
        $chargeBackAmount->setDay(1);
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('EUR');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();
    }

    private function generateMonthlyStats(\DateTime $dateTime)
    {
        $subscriptionCreationStat = new SubscriptionCreationMonthlyStats();
        $subscriptionCreationStat->setYear((int) $dateTime->format('Y'));
        $subscriptionCreationStat->setMonth((int) $dateTime->format('m'));
        $subscriptionCreationStat->setDay(1);
        $subscriptionCreationStat->setCount(random_int(1, 3000));
        $subscriptionCreationStat->setBrandCode(Customer::DEFAULT_BRAND);

        $subscriptionCancellation = new SubscriptionCancellationMonthlyStats();
        $subscriptionCancellation->setYear((int) $dateTime->format('Y'));
        $subscriptionCancellation->setMonth((int) $dateTime->format('m'));
        $subscriptionCancellation->setDay(1);
        $subscriptionCancellation->setCount(random_int(1, 3000));
        $subscriptionCancellation->setBrandCode(Customer::DEFAULT_BRAND);

        $paymentAmountStat = new PaymentAmountMonthlyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth((int) $dateTime->format('m'));
        $paymentAmountStat->setDay(1);
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('USD');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountMonthlyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth((int) $dateTime->format('m'));
        $refundAmount->setDay(1);
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('USD');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountMonthlyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth((int) $dateTime->format('m'));
        $chargeBackAmount->setDay(1);
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('USD');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCreationStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCancellation);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();

        $paymentAmountStat = new PaymentAmountMonthlyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth((int) $dateTime->format('m'));
        $paymentAmountStat->setDay(1);
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('EUR');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountMonthlyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth((int) $dateTime->format('m'));
        $refundAmount->setDay(1);
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('EUR');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountMonthlyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth((int) $dateTime->format('m'));
        $chargeBackAmount->setDay(1);
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('EUR');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();
    }

    private function generateDailyStats(\DateTime $dateTime)
    {
        $subscriptionCreationStat = new SubscriptionCreationDailyStats();
        $subscriptionCreationStat->setYear((int) $dateTime->format('Y'));
        $subscriptionCreationStat->setMonth((int) $dateTime->format('m'));
        $subscriptionCreationStat->setDay((int) $dateTime->format('d'));
        $subscriptionCreationStat->setCount(random_int(1, 3000));
        $subscriptionCreationStat->setBrandCode(Customer::DEFAULT_BRAND);

        $subscriptionCancellation = new SubscriptionCancellationDailyStats();
        $subscriptionCancellation->setYear((int) $dateTime->format('Y'));
        $subscriptionCancellation->setMonth((int) $dateTime->format('m'));
        $subscriptionCancellation->setDay((int) $dateTime->format('d'));
        $subscriptionCancellation->setCount(random_int(1, 3000));
        $subscriptionCancellation->setBrandCode(Customer::DEFAULT_BRAND);

        $paymentAmountStat = new PaymentAmountDailyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth((int) $dateTime->format('m'));
        $paymentAmountStat->setDay((int) $dateTime->format('d'));
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('USD');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountDailyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth((int) $dateTime->format('m'));
        $refundAmount->setDay((int) $dateTime->format('d'));
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('USD');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountDailyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth((int) $dateTime->format('m'));
        $chargeBackAmount->setDay((int) $dateTime->format('d'));
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('USD');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCreationStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($subscriptionCancellation);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();

        $paymentAmountStat = new PaymentAmountDailyStats();
        $paymentAmountStat->setYear((int) $dateTime->format('Y'));
        $paymentAmountStat->setMonth((int) $dateTime->format('m'));
        $paymentAmountStat->setDay((int) $dateTime->format('d'));
        $paymentAmountStat->setAmount(random_int(100, 300000));
        $paymentAmountStat->setCurrency('EUR');
        $paymentAmountStat->setBrandCode(Customer::DEFAULT_BRAND);

        $refundAmount = new RefundAmountDailyStats();
        $refundAmount->setYear((int) $dateTime->format('Y'));
        $refundAmount->setMonth((int) $dateTime->format('m'));
        $refundAmount->setDay((int) $dateTime->format('d'));
        $refundAmount->setAmount(random_int(100, 300000));
        $refundAmount->setCurrency('EUR');
        $refundAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $chargeBackAmount = new ChargeBackAmountDailyStats();
        $chargeBackAmount->setYear((int) $dateTime->format('Y'));
        $chargeBackAmount->setMonth((int) $dateTime->format('m'));
        $chargeBackAmount->setDay((int) $dateTime->format('d'));
        $chargeBackAmount->setAmount(random_int(100, 300000));
        $chargeBackAmount->setCurrency('EUR');
        $chargeBackAmount->setBrandCode(Customer::DEFAULT_BRAND);

        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($paymentAmountStat);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($refundAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->persist($chargeBackAmount);
        $this->refundAmountDailyStatsRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the overall stats
     */
    public function iViewTheOverallStats()
    {
        $this->sendJsonRequest('GET', '/app/stats');
    }

    /**
     * @Then I will see that there are stats for the default brand
     */
    public function iWillSeeThatThereAreStatsForTheDefaultBrand()
    {
        $data = $this->getJsonContent();

        if (!isset($data['subscription_creation']['daily'][Customer::DEFAULT_BRAND])) {
            throw new \Exception("Can't see subscritpion creation stats");
        }
    }

    /**
     * @Then I will see there is :arg1 days of daily stats
     */
    public function iWillSeeThereIsDaysOfDailyStats($arg1)
    {
        $data = $this->getJsonContent();
        $actual = count($data['subscription_creation']['daily'][Customer::DEFAULT_BRAND]);
        if ($actual != $arg1) {
            throw new \Exception('wrong count - '.$actual);
        }
    }

    /**
     * @Then I will see there is :arg1 months of monthly stats
     */
    public function iWillSeeThereIsMonthsOfMonthlyStats($arg1)
    {
        $data = $this->getJsonContent();
        if (count($data['subscription_creation']['monthly'][Customer::DEFAULT_BRAND]) != $arg1) {
            throw new \Exception('wrong count');
        }
    }

    /**
     * @Then I will see there is :arg1 years of yearly stats
     */
    public function iWillSeeThereIsYearsOfYearlyStats($arg1)
    {
        $data = $this->getJsonContent();
        if (count($data['subscription_creation']['yearly'][Customer::DEFAULT_BRAND]) < $arg1) {
            throw new \Exception('wrong count');
        }
    }

    /**
     * @Then I will see there is 12 months of monthly revenue stats for :currency
     */
    public function iWillSeeThereIsMonthsOfMonthlyRevenueStatsFor($currency)
    {
        $data = $this->getJsonContent();
        foreach ($data['payment_amount']['monthly'][Customer::DEFAULT_BRAND] as $month) {
            if (0 == $month[$currency]) {
                throw new \Exception('There is a month with a zero. There should be no months with a zero');
            }
        }
    }

    /**
     * @Then I will see the total number of active subscriptions
     */
    public function iWillSeeTheTotalNumberOfActiveSubscriptions()
    {
        $data = $this->getJsonContent();

        if (!isset($data['header']['active_subscriptions'])) {
            throw new \Exception("Can't see active_subscriptions");
        }
    }

    /**
     * @Then I will see the total number of active customers
     */
    public function iWillSeeTheTotalNumberOfActiveCustomers()
    {
        $data = $this->getJsonContent();

        if (!isset($data['header']['active_customers'])) {
            throw new \Exception("Can't see active_customers");
        }
    }

    /**
     * @Then I will see the number of outstanding payments
     */
    public function iWillSeeTheNumberOfOutstandingPayments()
    {
        $data = $this->getJsonContent();

        if (!isset($data['header']['unpaid_invoices_count'])) {
            throw new \Exception("Can't see unpaid invoices count");
        }

        if (!isset($data['header']['unpaid_invoices_amount'])) {
            throw new \Exception("Can't see unpaid invoices amount");
        }
    }

    /**
     * @Then the monthly recurring revenue estimate should be :arg1
     */
    public function theMonthlyRecurringRevenueEstimateShouldBe($arg1)
    {
        /** @var CachedStats $cached */
        $cached = $this->cachedStatsRepository->findOneBy(['name' => CachedStats::STAT_NAME_ESTIMATED_MRR]);

        if (!$cached instanceof CachedStats) {
            throw new \Exception("Can't find stat");
        }

        if ($cached->getValue() != $arg1) {
            throw new \Exception('Incorrect value - '.$cached->getValue());
        }
    }

    /**
     * @Then the annual recurring revenue estimate should be :arg1
     */
    public function theAnnualRecurringRevenueEstimateShouldBe($arg1)
    {
        /** @var CachedStats $cached */
        $cached = $this->cachedStatsRepository->findOneBy(['name' => CachedStats::STAT_NAME_ESTIMATED_ARR]);

        if (!$cached instanceof CachedStats) {
            throw new \Exception("Can't find stat");
        }

        if ($cached->getValue() != $arg1) {
            throw new \Exception('Incorrect value - '.$cached->getValue());
        }
    }
}
