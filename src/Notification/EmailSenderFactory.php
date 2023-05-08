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

namespace App\Notification;

use App\Entity\Settings\NotificationSettings;
use App\Repository\SettingsRepositoryInterface;
use Mailgun\Mailgun;
use Parthenon\Notification\Configuration;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Sender\MailgunEmailSender;
use Parthenon\Notification\Sender\PostmarkEmailSender;
use Parthenon\Notification\Sender\SendGridEmailSender;
use Parthenon\Notification\Sender\SymfonyEmailSender;
use Postmark\PostmarkClient;
use Symfony\Component\Mailer\MailerInterface;

class EmailSenderFactory
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository, private MailerInterface $mailer)
    {
    }

    public function create(): EmailSenderInterface
    {
        $notificationSettings = $this->settingsRepository->getDefaultSettings()->getNotificationSettings();

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

        return new SendGridEmailSender($sendGrid, $config);
    }

    private function createConfiguration(NotificationSettings $notificationSettings): Configuration
    {
        $config = new Configuration('BillaBear', $notificationSettings->getDefaultOutgoingEmail());

        return $config;
    }

    private function createPostMark(NotificationSettings $notificationSettings): PostmarkEmailSender
    {
        $postmarkClient = new PostmarkClient($notificationSettings->getEmspApiKey());

        return new PostmarkEmailSender($postmarkClient);
    }

    private function createMailgun(NotificationSettings $notificationSettings): MailgunEmailSender
    {
        $mailGun = Mailgun::create($notificationSettings->getEmspApiKey());

        return new MailgunEmailSender($mailGun, $notificationSettings->getEmspDomain(), $this->createConfiguration($notificationSettings));
    }

    private function createSymfony(NotificationSettings $notificationSettings): SymfonyEmailSender
    {
        return new SymfonyEmailSender($this->mailer, $this->createConfiguration($notificationSettings));
    }
}
