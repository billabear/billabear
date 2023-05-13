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

namespace App\Tests\Unit\Stats\Graph;

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

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(12, $output[Customer::DEFAULT_BRAND]['2023-01-12']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']['USD']);
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

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToDailyOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(31, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(31, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-01-12']['USD']);
        $this->assertEquals(12, $output['Brand Two']['2023-01-12']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-01-31']['USD']);
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

        $subject = new MoneyStatOutputConverter();
        $output = $subject->convertToMonthOutput($startDate, $endDate, [$startStat, $middleStat, $endStat]);

        $this->assertCount(2, $output);
        $this->assertCount(12, $output[Customer::DEFAULT_BRAND]);
        $this->assertCount(12, $output['Brand Two']);
        $this->assertEquals(11, $output[Customer::DEFAULT_BRAND]['2023-01-01']['USD']);
        $this->assertEquals(0, $output[Customer::DEFAULT_BRAND]['2023-02-01']['USD']);
        $this->assertEquals(12, $output['Brand Two']['2023-03-01']['USD']);
        $this->assertEquals(16, $output[Customer::DEFAULT_BRAND]['2023-12-01']['USD']);
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