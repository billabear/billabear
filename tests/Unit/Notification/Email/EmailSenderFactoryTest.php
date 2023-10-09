<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Notification\Email;

use App\Entity\Settings;
use App\Notification\Email\EmailSenderFactory;
use App\Repository\SettingsRepositoryInterface;
use Parthenon\Notification\Sender\MailgunEmailSender;
use Parthenon\Notification\Sender\PostmarkEmailSender;
use Parthenon\Notification\Sender\SendGridEmailSender;
use Parthenon\Notification\Sender\SymfonyEmailSender;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class EmailSenderFactoryTest extends TestCase
{
    public function testReturnsSendgridSender()
    {
        $mailer = $this->createMock(MailerInterface::class);

        $settings = $this->createMock(Settings::class);
        $notificationSettings = $this->createMock(Settings\NotificationSettings::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);
        $settings->method('getNotificationSettings')->willReturn($notificationSettings);

        $notificationSettings->method('getEmsp')->willReturn(Settings\NotificationSettings::EMSP_SENDGRID);
        $notificationSettings->method('getEmspApiKey')->willReturn('API-KEY');
        $notificationSettings->method('getDefaultOutgoingEmail')->willReturn('billabear@example.org');

        $subject = new EmailSenderFactory($settingsRepository, $mailer);
        $sender = $subject->create();
        $this->assertInstanceOf(SendGridEmailSender::class, $sender);
    }

    public function testReturnsPostmarkSender()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);
        $settings = $this->createMock(Settings::class);
        $notificationSettings = $this->createMock(Settings\NotificationSettings::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);
        $settings->method('getNotificationSettings')->willReturn($notificationSettings);

        $notificationSettings->method('getEmsp')->willReturn(Settings\NotificationSettings::EMSP_POSTMARK);
        $notificationSettings->method('getEmspApiKey')->willReturn('API-KEY');
        $notificationSettings->method('getDefaultOutgoingEmail')->willReturn('billabear@example.org');

        $subject = new EmailSenderFactory($settingsRepository, $mailer);
        $sender = $subject->create();
        $this->assertInstanceOf(PostmarkEmailSender::class, $sender);
    }

    public function testReturnsMailgunSender()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $settings = $this->createMock(Settings::class);
        $notificationSettings = $this->createMock(Settings\NotificationSettings::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);
        $settings->method('getNotificationSettings')->willReturn($notificationSettings);

        $notificationSettings->method('getEmsp')->willReturn(Settings\NotificationSettings::EMSP_MAILGUN);
        $notificationSettings->method('getEmspApiKey')->willReturn('API-KEY');
        $notificationSettings->method('getEmspDomain')->willReturn('Domain.com');
        $notificationSettings->method('getDefaultOutgoingEmail')->willReturn('billabear@example.org');

        $subject = new EmailSenderFactory($settingsRepository, $mailer);
        $sender = $subject->create();
        $this->assertInstanceOf(MailgunEmailSender::class, $sender);
    }

    public function testReturnsSymfonySender()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $settings = $this->createMock(Settings::class);
        $notificationSettings = $this->createMock(Settings\NotificationSettings::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);
        $settings->method('getNotificationSettings')->willReturn($notificationSettings);

        $notificationSettings->method('getEmsp')->willReturn(Settings\NotificationSettings::EMSP_SYSTEM);
        $notificationSettings->method('getEmspApiKey')->willReturn('API-KEY');
        $notificationSettings->method('getEmspDomain')->willReturn('Domain.com');
        $notificationSettings->method('getDefaultOutgoingEmail')->willReturn('billabear@example.org');

        $subject = new EmailSenderFactory($settingsRepository, $mailer);
        $sender = $subject->create();
        $this->assertInstanceOf(SymfonyEmailSender::class, $sender);
    }
}
