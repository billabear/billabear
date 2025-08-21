<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Notification\Email;

use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Notification\Email\EmailTemplateProvider;
use BillaBear\Repository\EmailTemplateRepositoryInterface;
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
        $actual = $subject->getTemplateForCustomer($customer, EmailTemplate::NAME_SUBSCRIPTION_CREATED);
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
        $actual = $subject->getTemplateForCustomer($customer, EmailTemplate::NAME_SUBSCRIPTION_CREATED);
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
        $actual = $subject->getTemplateForCustomer($customer, EmailTemplate::NAME_SUBSCRIPTION_CREATED);
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
        $actual = $subject->getTemplateForCustomer($customer, EmailTemplate::NAME_SUBSCRIPTION_CREATED);
    }
}
