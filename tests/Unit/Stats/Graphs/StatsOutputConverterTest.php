<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Stats\Graphs;

use App\Entity\Customer;
use App\Entity\Stats\SubscriptionCreationDailyStats;
use App\Stats\Graphs\StatOutputConverter;
use PHPUnit\Framework\TestCase;

class StatsOutputConverterTest extends TestCase
{
    public function testSingleMonthDaily()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-01-31');

        $startStat = new SubscriptionCreationDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setCount(11);

        $middleStat = new SubscriptionCreationDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode(Customer::DEFAULT_BRAND);
        $middleStat->setCount(12);

        $endStat = new SubscriptionCreationDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setCount(16);

        $subject = new StatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']);
        $this->assertEquals(12, $output[Customer::DEFAULT_BRAND]['2023-01-12']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']);
    }

    public function testMultiMonthSpan()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-03-01');

        $startStat = new SubscriptionCreationDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setCount(11);

        $middleStat = new SubscriptionCreationDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode(Customer::DEFAULT_BRAND);
        $middleStat->setCount(12);

        $endStat = new SubscriptionCreationDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setCount(16);

        $subject = new StatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(60, $output[Customer::DEFAULT_BRAND]);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']);
        $this->assertEquals(12, $output[Customer::DEFAULT_BRAND]['2023-01-12']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']);
    }

    public function testSingleMonthDailyTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-01-31');

        $startStat = new SubscriptionCreationDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setCount(11);

        $middleStat = new SubscriptionCreationDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setCount(12);

        $endStat = new SubscriptionCreationDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setCount(16);

        $subject = new StatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(31, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-12']);
        $this->assertEquals(12, $output['Brand Two']['2023-01-12']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']);
    }

    public function testSingleYearTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-12-31');

        $startStat = new SubscriptionCreationDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setCount(11);

        $middleStat = new SubscriptionCreationDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(03);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setCount(12);

        $endStat = new SubscriptionCreationDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(12);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setCount(16);

        $subject = new StatOutputConverter();
        $output = $subject->convertToMonthOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(12, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(12, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-02-01']);
        $this->assertEquals(12, $output['Brand Two']['2023-03-01']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-12-01']);
    }

    public function testSingleDecadeTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2032-12-31');

        $startStat = new SubscriptionCreationDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setCount(11);

        $middleStat = new SubscriptionCreationDailyStats();
        $middleStat->setYear(2025);
        $middleStat->setMonth(03);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setCount(12);

        $endStat = new SubscriptionCreationDailyStats();
        $endStat->setYear(2028);
        $endStat->setMonth(12);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setCount(16);

        $subject = new StatOutputConverter();
        $output = $subject->convertToYearOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(10, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(10, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2024-01-01']);
        $this->assertEquals(12, $output['Brand Two']['2025-01-01']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2028-01-01']);
    }
}
