<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Stats;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Entity\Stats\ChargeBackAmountDailyStats;
use BillaBear\Entity\Stats\ChargeBackAmountMonthlyStats;
use BillaBear\Entity\Stats\ChargeBackAmountYearlyStats;
use BillaBear\Entity\Stats\PaymentAmountDailyStats;
use BillaBear\Entity\Stats\PaymentAmountMonthlyStats;
use BillaBear\Entity\Stats\PaymentAmountYearlyStats;
use BillaBear\Entity\Stats\RefundAmountDailyStats;
use BillaBear\Entity\Stats\RefundAmountMonthlyStats;
use BillaBear\Entity\Stats\RefundAmountYearlyStats;
use BillaBear\Entity\Stats\SubscriptionCancellationDailyStats;
use BillaBear\Entity\Stats\SubscriptionCancellationMonthlyStats;
use BillaBear\Entity\Stats\SubscriptionCancellationYearlyStats;
use BillaBear\Entity\Stats\SubscriptionCreationDailyStats;
use BillaBear\Entity\Stats\SubscriptionCreationMonthlyStats;
use BillaBear\Entity\Stats\SubscriptionCreationYearlyStats;
use BillaBear\Entity\Stats\TrialExtendedDailyStats;
use BillaBear\Entity\Stats\TrialExtendedMonthlyStats;
use BillaBear\Entity\Stats\TrialExtendedYearlyStats;
use BillaBear\Entity\Stats\TrialStartedDailyStats;
use BillaBear\Entity\Stats\TrialStartedMonthlyStats;
use BillaBear\Entity\Stats\TrialStartedYearlyStats;
use BillaBear\Repository\Orm\CachedStatsRepository;
use BillaBear\Repository\Orm\PaymentAmountDailyStatsRepository;
use BillaBear\Repository\Orm\RefundAmountDailyStatsRepository;
use BillaBear\Repository\Orm\SubscriptionCreationDailyStatsRepository;
use BillaBear\Repository\Orm\SubscriptionCreationMonthlyStatsRepository;
use BillaBear\Repository\Orm\SubscriptionCreationYearlyStatsRepository;
use BillaBear\Repository\Orm\TrialExtendedDailyStatsRepository;
use BillaBear\Repository\Orm\TrialExtendedMonthlyStatsRepository;
use BillaBear\Repository\Orm\TrialExtendedYearlyStatsRepository;
use BillaBear\Repository\Orm\TrialStartedDailyStatsRepository;
use BillaBear\Repository\Orm\TrialStartedMonthlyStatsRepository;
use BillaBear\Repository\Orm\TrialStartedYearlyStatsRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class MainContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SubscriptionCreationDailyStatsRepository $subscriptionDailyStatsRepository,
        private SubscriptionCreationMonthlyStatsRepository $subscriptionCreationMonthlyStatsRepository,
        private SubscriptionCreationYearlyStatsRepository $subscriptionCreationYearlyStatsRepository,
        private TrialStartedDailyStatsRepository $trialStartedDailyStatsRepository,
        private TrialStartedMonthlyStatsRepository $trialStartedMonthlyStatsRepository,
        private TrialStartedYearlyStatsRepository $trialStartedYearlyStatsRepository,
        private TrialExtendedDailyStatsRepository $trialExtendedDailyStatsRepository,
        private TrialExtendedMonthlyStatsRepository $trialExtendedMonthlyStatsRepository,
        private TrialExtendedYearlyStatsRepository $trialExtendedYearlyStatsRepository,
        private PaymentAmountDailyStatsRepository $paymentAmountDailyStatsRepository,
        private RefundAmountDailyStatsRepository $refundAmountDailyStatsRepository,
        private CachedStatsRepository $cachedStatsRepository,
    ) {
    }

    /**
     * @Then the trial started daily stat for the day should be :arg1
     */
    public function theTrialStartedDailyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialStartedDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
        ]);

        if (!$statEntity instanceof TrialStartedDailyStats && 0 !== intval($count)) {
            var_dump($this->getJsonContent());
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 !== intval($count)) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the trial started monthly stat for the day should be :arg1
     */
    public function theTrialStartedMonthlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialStartedMonthlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => 1,
        ]);

        if (!$statEntity instanceof TrialStartedMonthlyStats && 0 !== intval($count)) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 !== intval($count)) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the trial started yearly stat for the day should be :arg1
     */
    public function theTrialStartedYearlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialStartedYearlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => 1,
            'day' => 1,
        ]);

        if (!$statEntity instanceof TrialStartedYearlyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the trial extended daily stat for the day should be :arg1
     */
    public function theTrialExtendedDailyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialExtendedDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
        ]);

        if (!$statEntity instanceof TrialExtendedDailyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the trial extended monthly stat for the day should be :arg1
     */
    public function theTrialExtendedMonthlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialExtendedMonthlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => 1,
        ]);

        if (!$statEntity instanceof TrialExtendedMonthlyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the trial extended yearly stat for the day should be :arg1
     */
    public function theTrialExtendedYearlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->trialExtendedYearlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => 1,
            'day' => 1,
        ]);

        if (!$statEntity instanceof TrialExtendedYearlyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
            throw new \Exception('Count is wrong');
        }
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

        if (!$statEntity instanceof SubscriptionCreationDailyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
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

        if (!$statEntity instanceof SubscriptionCreationMonthlyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
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

        if (!$statEntity instanceof SubscriptionCreationYearlyStats && 0 != $count) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getCount() != $count && 0 != $count) {
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

        if (!$statEntity instanceof PaymentAmountDailyStats && 0 != $amount) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getAmount() != $amount && 0 != $amount) {
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $statEntity->getAmount()));
        }
        if ($statEntity?->getCurrency() != $currency && 0 != $amount) {
            throw new \Exception('Currency is wrong');
        }
    }

    /**
     * @Then the payment amount stats for the day should be more than :arg2 and less than :arg3 in the currency :arg1
     */
    public function thePaymentAmountStatsForTheDayShouldBeMoreThanAndLessThanInTheCurrency($amount, $higherAmount, $currency): void
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->paymentAmountDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
        ]);

        if (!$statEntity instanceof PaymentAmountDailyStats && 0 != $amount) {
            throw new \Exception('No stat found');
        }

        if ($statEntity?->getAmount() <= $amount || $statEntity->getAmount() >= $higherAmount) {
            throw new \Exception(sprintf('Expected between %d and %d but got %d', $amount, $higherAmount, $statEntity->getAmount()));
        }

        if ($statEntity?->getCurrency() != $currency && 0 != $amount) {
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
    public function theMonthlyRecurringRevenueEstimateShouldBe($amount)
    {
        /** @var CachedStats $cached */
        $cached = $this->cachedStatsRepository->findOneBy(['name' => CachedStats::STAT_NAME_ESTIMATED_MRR]);

        if (!$cached instanceof CachedStats && 0 != $amount) {
            throw new \Exception("Can't find stat");
        }

        if ($cached?->getValue() != $amount && 0 != $amount) {
            throw new \Exception('Incorrect value - '.$cached->getValue());
        }
    }

    /**
     * @Then the annual recurring revenue estimate should be :arg1
     */
    public function theAnnualRecurringRevenueEstimateShouldBe($amount)
    {
        /** @var CachedStats $cached */
        $cached = $this->cachedStatsRepository->findOneBy(['name' => CachedStats::STAT_NAME_ESTIMATED_ARR]);

        if (!$cached instanceof CachedStats && 0 != $amount) {
            throw new \Exception("Can't find stat");
        }

        if ($cached?->getValue() != $amount && 0 != $amount) {
            throw new \Exception('Incorrect value - '.$cached->getValue());
        }
    }
}
