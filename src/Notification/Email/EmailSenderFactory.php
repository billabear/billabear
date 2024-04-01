<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Notification\Email;

use App\Entity\Settings\NotificationSettings;
use App\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Mailgun\Mailgun;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Sender\MailgunEmailSender;
use Parthenon\Notification\Sender\NullEmailSender;
use Parthenon\Notification\Sender\PostmarkEmailSender;
use Parthenon\Notification\Sender\SendGridEmailSender;
use Parthenon\Notification\Sender\SymfonyEmailSender;
use Postmark\PostmarkClient;
use Symfony\Component\Mailer\MailerInterface;

class EmailSenderFactory
{
    use LoggerAwareTrait;

    public function __construct(private SettingsRepositoryInterface $settingsRepository, private MailerInterface $mailer)
    {
    }

    public function create(): EmailSenderInterface
    {
        try {
            $notificationSettings = $this->settingsRepository->getDefaultSettings()->getNotificationSettings();
        } catch (TableNotFoundException $e) {
            return new NullEmailSender();
        }

        switch ($notificationSettings->getEmsp()) {
            case NotificationSettings::EMSP_SENDGRID:
                return $this->createSendGrid($notificationSettings);
            case NotificationSettings::EMSP_POSTMARK:
                return $this->createPostMark($notificationSettings);
            case NotificationSettings::EMSP_MAILGUN:
                return $this->createMailgun($notificationSettings);
            default:
                return $this->createSymfony($notificationSettings);
        }
    }

    private function createSendGrid(NotificationSettings $notificationSettings): SendGridEmailSender
    {
        $sendGrid = new \SendGrid($notificationSettings->getEmspApiKey());
        $config = $this->createConfiguration($notificationSettings);

        $sendGridSender = new SendGridEmailSender($sendGrid, $config);
        $sendGridSender->setLogger($this->getLogger());

        return $sendGridSender;
    }

    private function createConfiguration(NotificationSettings $notificationSettings): Configuration
    {
        $config = new Configuration('BillaBear System', $notificationSettings->getDefaultOutgoingEmail());

        return $config;
    }

    private function createPostMark(NotificationSettings $notificationSettings): PostmarkEmailSender
    {
        $postmarkClient = new PostmarkClient($notificationSettings->getEmspApiKey());

        $sender = new PostmarkEmailSender($postmarkClient);
        $sender->setLogger($this->getLogger());

        return $sender;
    }

    private function createMailgun(NotificationSettings $notificationSettings): MailgunEmailSender
    {
        $mailGun = Mailgun::create($notificationSettings->getEmspApiKey());

        $sender = new MailgunEmailSender($mailGun, $notificationSettings->getEmspDomain(), $this->createConfiguration($notificationSettings));
        $sender->setLogger($this->getLogger());

        return $sender;
    }

    private function createSymfony(NotificationSettings $notificationSettings): SymfonyEmailSender
    {
        return new SymfonyEmailSender($this->mailer, $this->createConfiguration($notificationSettings));
    }
}
