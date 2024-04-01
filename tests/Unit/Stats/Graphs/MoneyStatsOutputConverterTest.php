<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Stats\Graphs;

use App\Entity\Customer;
use App\Entity\Stats\PaymentAmountDailyStats;
use App\Stats\Graphs\MoneyStatOutputConverter;
use PHPUnit\Framework\TestCase;

class MoneyStatsOutputConverterTest extends TestCase
{
    public function testSingleMonthDaily()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-01-31');

        $startStat = new PaymentAmountDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setAmount(11);
        $startStat->setCurrency('USD');

        $middleStat = new PaymentAmountDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode(Customer::DEFAULT_BRAND);
        $middleStat->setAmount(12);
        $middleStat->setCurrency('USD');

        $endStat = new PaymentAmountDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setAmount(16);
        $endStat->setCurrency('USD');

        $endStatEur = new PaymentAmountDailyStats();
        $endStatEur->setYear(2023);
        $endStatEur->setMonth(01);
        $endStatEur->setDay(31);
        $endStatEur->setBrandCode(Customer::DEFAULT_BRAND);
        $endStatEur->setAmount(1632);
        $endStatEur->setCurrency('EUR');

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat, $endStatEur]);

        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(12, $output[Customer::DEFAULT_BRAND]['2023-01-12']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']['USD']);
        $this->assertEquals(1632, $output[Customer::DEFAULT_BRAND]['2023-01-31']['EUR']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']['USD']);
    }

    public function testMultiMonthSpan()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-03-01');

        $startStat = new PaymentAmountDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setAmount(11);
        $startStat->setCurrency('USD');

        $middleStat = new PaymentAmountDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode(Customer::DEFAULT_BRAND);
        $middleStat->setAmount(12);
        $middleStat->setCurrency('USD');

        $endStat = new PaymentAmountDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setAmount(16);
        $endStat->setCurrency('USD');

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(60, $output[Customer::DEFAULT_BRAND]);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(12, $output[Customer::DEFAULT_BRAND]['2023-01-12']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']['USD']);
    }

    public function testSingleMonthDailyTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-01-31');

        $startStat = new PaymentAmountDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setAmount(11);
        $startStat->setCurrency('USD');

        $middleStat = new PaymentAmountDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(01);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setAmount(12);
        $middleStat->setCurrency('USD');

        $endStat = new PaymentAmountDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(01);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setAmount(16);
        $endStat->setCurrency('USD');

        $endStatEuro = new PaymentAmountDailyStats();
        $endStatEuro->setYear(2023);
        $endStatEuro->setMonth(01);
        $endStatEuro->setDay(31);
        $endStatEuro->setBrandCode(Customer::DEFAULT_BRAND);
        $endStatEuro->setAmount(1632);
        $endStatEuro->setCurrency('EUR');

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat, $endStatEuro]);

        $this->assertCount(2, $output);
        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(31, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-12']['USD']);
        $this->assertEquals(12, $output['Brand Two']['2023-01-12']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']['USD']);
        $this->assertEquals(1632, $output[Customer::DEFAULT_BRAND]['2023-01-31']['EUR']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-02']['USD']);
    }

    public function testSingleYearTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-12-31');

        $startStat = new PaymentAmountDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setAmount(11);
        $startStat->setCurrency('USD');

        $middleStat = new PaymentAmountDailyStats();
        $middleStat->setYear(2023);
        $middleStat->setMonth(03);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setAmount(12);
        $middleStat->setCurrency('USD');

        $endStat = new PaymentAmountDailyStats();
        $endStat->setYear(2023);
        $endStat->setMonth(12);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setAmount(16);
        $endStat->setCurrency('USD');

        $endStatEuro = new PaymentAmountDailyStats();
        $endStatEuro->setYear(2023);
        $endStatEuro->setMonth(12);
        $endStatEuro->setDay(31);
        $endStatEuro->setBrandCode(Customer::DEFAULT_BRAND);
        $endStatEuro->setAmount(1634);
        $endStatEuro->setCurrency('EUR');

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToMonthOutput($startDate, $endDate, [$startStat, $middleStat, $endStat, $endStatEuro]);

        $this->assertCount(2, $output);
        $this->assertCount(12, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(12, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-02-01']['USD']);
        $this->assertEquals(12, $output['Brand Two']['2023-03-01']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-12-01']['USD']);
        $this->assertEquals(1634, $output[Customer::DEFAULT_BRAND]['2023-12-01']['EUR']);
    }

    public function testSingleDecadeTwoBrands()
    {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2032-12-31');

        $startStat = new PaymentAmountDailyStats();
        $startStat->setYear(2023);
        $startStat->setMonth(01);
        $startStat->setDay(01);
        $startStat->setBrandCode(Customer::DEFAULT_BRAND);
        $startStat->setAmount(11);
        $startStat->setCurrency('USD');

        $middleStat = new PaymentAmountDailyStats();
        $middleStat->setYear(2025);
        $middleStat->setMonth(03);
        $middleStat->setDay(12);
        $middleStat->setBrandCode('Brand Two');
        $middleStat->setAmount(12);
        $middleStat->setCurrency('USD');

        $endStat = new PaymentAmountDailyStats();
        $endStat->setYear(2028);
        $endStat->setMonth(12);
        $endStat->setDay(31);
        $endStat->setBrandCode(Customer::DEFAULT_BRAND);
        $endStat->setAmount(16);
        $endStat->setCurrency('USD');

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToYearOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(10, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(10, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2024-01-01']['USD']);
        $this->assertEquals(12, $output['Brand Two']['2025-01-01']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2028-01-01']['USD']);
    }
}
