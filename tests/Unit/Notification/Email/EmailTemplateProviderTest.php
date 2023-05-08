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

namespace App\Tests\Unit\Notification\Email;

use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Notification\Email\EmailTemplateProvider;
use App\Repository\EmailTemplateRepositoryInterface;
use PHPUnit\Framework\TestCase;

class EmailTemplateProviderTest extends TestCase
{
    public const LOCALE = 'de';
    public const BRAND = 'humblyarrogant';

    public function testGetEmail()
    {
        $customer = $this->createMock(Customer::class);
        $customer->method('getLocale')->willReturn(self::LOCALE);
        $customer->method('getBrand')->willReturn(self::BRAND);

        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplateRepository = $this->createMock(EmailTemplateRepositoryInterface::class);
        $emailTemplateRepository
            ->expects($this->once())
            ->method('getByNameAndLocaleAndBrand')
            ->with(EmailTemplate::NAME_SUBSCRIPTION_CREATED, self::LOCALE, self::BRAND)
            ->willReturn($emailTemplate);

        $subject = new EmailTemplateProvider($emailTemplateRepository);
        $actual = $subject->createTemplateForCustomer(EmailTemplate::NAME_SUBSCRIPTION_CREATED, $customer);
        $this->assertEquals($emailTemplate, $actual);
    }

    public function testGetEmailDefaultLocale()
    {
        $customer = $this->createMock(Customer::class);
        $customer->method('getLocale')->willReturn(self::LOCALE);
        $customer->method('getBrand')->willReturn(self::BRAND);

        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplateRepository = $this->createMock(EmailTemplateRepositoryInterface::class);
        $emailTemplateRepository
            ->expects($this->exactly(2))
            ->method('getByNameAndLocaleAndBrand')
            ->will($this->returnValueMap(
                [
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, self::LOCALE, self::BRAND, null],
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, Customer::DEFAULT_LOCALE, self::BRAND, $emailTemplate],
                ]
            ));

        $subject = new EmailTemplateProvider($emailTemplateRepository);
        $actual = $subject->createTemplateForCustomer(EmailTemplate::NAME_SUBSCRIPTION_CREATED, $customer);
        $this->assertEquals($emailTemplate, $actual);
    }

    public function testGetEmailDefaultLocaleAndDefaultBrand()
    {
        $customer = $this->createMock(Customer::class);
        $customer->method('getLocale')->willReturn(self::LOCALE);
        $customer->method('getBrand')->willReturn(self::BRAND);

        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplateRepository = $this->createMock(EmailTemplateRepositoryInterface::class);
        $emailTemplateRepository
            ->expects($this->exactly(3))
            ->method('getByNameAndLocaleAndBrand')
            ->will($this->returnValueMap(
                [
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, self::LOCALE, self::BRAND, null],
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, Customer::DEFAULT_LOCALE, self::BRAND, null],
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND, $emailTemplate],
                ]
            ));

        $subject = new EmailTemplateProvider($emailTemplateRepository);
        $actual = $subject->createTemplateForCustomer(EmailTemplate::NAME_SUBSCRIPTION_CREATED, $customer);
        $this->assertEquals($emailTemplate, $actual);
    }

    public function testNoTemplateFoundExceptionFlung()
    {
        $this->expectException(\Exception::class);
        $customer = $this->createMock(Customer::class);
        $customer->method('getLocale')->willReturn(self::LOCALE);
        $customer->method('getBrand')->willReturn(self::BRAND);

        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplateRepository = $this->createMock(EmailTemplateRepositoryInterface::class);
        $emailTemplateRepository
            ->expects($this->exactly(3))
            ->method('getByNameAndLocaleAndBrand')
            ->will($this->returnValueMap(
                [
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, self::LOCALE, self::BRAND, null],
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, Customer::DEFAULT_LOCALE, self::BRAND, null],
                    [EmailTemplate::NAME_SUBSCRIPTION_CREATED, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND, null],
                ]
            ));

        $subject = new EmailTemplateProvider($emailTemplateRepository);
        $actual = $subject->createTemplateForCustomer(EmailTemplate::NAME_SUBSCRIPTION_CREATED, $customer);
    }
}
