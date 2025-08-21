<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Entity\Settings\NotificationSettings;
use BillaBear\Repository\SettingsRepositoryInterface;
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

class EmailSenderFactory implements EmailSenderFactoryInterface
{
    use LoggerAwareTrait;

    private bool $enabled = true;

    public function __construct(private SettingsRepositoryInterface $settingsRepository, private MailerInterface $mailer)
    {
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function create(): EmailSenderInterface
    {
        try {
            $notificationSettings = $this->settingsRepository->getDefaultSettings()->getNotificationSettings();
        } catch (TableNotFoundException $e) {
            return new NullEmailSender();
        }

        // Added to stop emails being sent during batch re-processing like imports.
        if (!$this->enabled) {
            $this->getLogger()->debug('Email sending is disabled so null email sender returned');

            return new NullEmailSender();
        }

        return match ($notificationSettings->getEmsp()) {
            NotificationSettings::EMSP_SENDGRID => $this->createSendGrid($notificationSettings),
            NotificationSettings::EMSP_POSTMARK => $this->createPostMark($notificationSettings),
            NotificationSettings::EMSP_MAILGUN => $this->createMailgun($notificationSettings),
            default => $this->createSymfony($notificationSettings),
        };
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
        return new Configuration('BillaBear System', $notificationSettings->getDefaultOutgoingEmail());
    }

    private function createPostMark(NotificationSettings $notificationSettings): PostmarkEmailSender
    {
        $postmarkClient = new PostmarkClient($notificationSettings->getEmspApiKey());

        $sender = new PostmarkEmailSender($postmarkClient, $this->createConfiguration($notificationSettings));
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
