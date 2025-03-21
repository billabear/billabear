<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Notification\Email;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Notification\Email\Data\SubscriptionCreatedEmailData;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Notification\Email\EmailTemplateProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

class EmailBuilderTest extends TestCase
{
    public function testEmailEmspTemplate()
    {
        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplate->method('getTemplateId')->willReturn('template-id');

        $emailTemplateProvider = $this->createMock(EmailTemplateProvider::class);
        $emailTemplateProvider->method('getTemplateForCustomer')->willReturn($emailTemplate);
        $twig = $this->createMock(Environment::class);

        $brandSettings = $this->createMock(BrandSettings::class);
        $brandSettings->method('getEmailAddress')->willReturn('brand@example.org');
        $brandSettings->method('getBrandName')->willReturn('Brandy McBrandFace');

        $customer = $this->createMock(Customer::class);
        $customer->method('getBillingEmail')->willReturn('iain@example.com');
        $customer->method('getBrandSettings')->willReturn($brandSettings);
        $emailData = $this->createMock(SubscriptionCreatedEmailData::class);
        $emailData->method('getTemplateName')->willReturn(EmailTemplate::NAME_SUBSCRIPTION_CREATED);
        $emailData->method('getData')->willReturn([]);

        $subject = new EmailBuilder($twig, $emailTemplateProvider);
        $actual = $subject->build($customer, $emailData);

        $this->assertEquals('Brandy McBrandFace', $actual->getFromName());
        $this->assertEquals('brand@example.org', $actual->getFromAddress());
        $this->assertEquals('template-id', $actual->getTemplateName());
        $this->assertTrue($actual->isTemplate());
    }

    public function testEmailTwig()
    {
        $emailTemplate = $this->createMock(EmailTemplate::class);
        $emailTemplate->method('getTemplateId')->willReturn(null);
        $emailTemplate->method('getTemplateBody')->willReturn('template-body');
        $emailTemplate->method('getSubject')->willReturn('Subject');

        $emailTemplateProvider = $this->createMock(EmailTemplateProvider::class);
        $emailTemplateProvider->method('getTemplateForCustomer')->willReturn($emailTemplate);
        $twig = $this->createMock(Environment::class);

        $brandSettings = $this->createMock(BrandSettings::class);
        $brandSettings->method('getEmailAddress')->willReturn('brand@example.org');
        $brandSettings->method('getBrandName')->willReturn('Brandy McBrandFace');

        $customer = $this->createMock(Customer::class);
        $customer->method('getBillingEmail')->willReturn('iain@example.com');
        $customer->method('getBrandSettings')->willReturn($brandSettings);
        $emailData = $this->createMock(SubscriptionCreatedEmailData::class);
        $emailData->method('getTemplateName')->willReturn(EmailTemplate::NAME_SUBSCRIPTION_CREATED);
        $emailData->method('getData')->willReturn([]);

        $templateWrapper = $this->createMock(Template::class);

        $twig->method('createTemplate')->willReturn(new TemplateWrapper($twig, $templateWrapper));
        $twig->method('render')->willReturn('template-compiled');

        $subject = new EmailBuilder($twig, $emailTemplateProvider);
        $actual = $subject->build($customer, $emailData);

        $this->assertEquals('Brandy McBrandFace', $actual->getFromName());
        $this->assertEquals('brand@example.org', $actual->getFromAddress());
        $this->assertEquals('template-compiled', $actual->getContent());
        $this->assertFalse($actual->isTemplate());
    }
}
