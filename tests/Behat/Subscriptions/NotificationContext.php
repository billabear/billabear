<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Subscriptions;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use BillaBear\Background\Notifications\TrialEndingWarning;
use BillaBear\Entity\Customer;
use BillaBear\Repository\Orm\BrandSettingsRepository;
use BillaBear\Tests\Mock\EmailSender;

class NotificationContext implements Context
{
    public function __construct(
        private TrialEndingWarning $endingWarning,
        private EmailSender $emailSender,
        private BrandSettingsRepository $brandSettingsRepository,
    ) {
    }

    #[Given('that trial ending emails are to be sent')]
    public function thatTrialEndingEmailsAreToBeSent(): void
    {
        $brand = $this->brandSettingsRepository->findOneBy(['code' => Customer::DEFAULT_BRAND]);
        $brand->getNotificationSettings()->setSendTrialEndingWarning(true);
        $this->brandSettingsRepository->getEntityManager()->persist($brand);
        $this->brandSettingsRepository->getEntityManager()->flush();
    }

    #[When('the trial ending check is ran')]
    public function theTrialEndingCheckIsRan(): void
    {
        $this->endingWarning->execute();
    }

    #[Then('the trial ending warning email is sent to the customer')]
    public function theTrialEndingWarningEmailIsSentToTheCustomer(): void
    {
        $emails = $this->emailSender->getEmails();

        if (0 === count($emails)) {
            throw new \Exception('No emails were sent');
        }

        foreach ($emails as $email) {
            if ('Trial Ending Soon' === $email->getSubject()) {
                return;
            }
        }

        throw new \Exception("No email with subject 'Trial Ending Soon' was sent");
    }

    #[Given('that trial ending emails are not to be sent')]
    public function thatTrialEndingEmailsAreNotToBeSent(): void
    {
        $brand = $this->brandSettingsRepository->findOneBy(['code' => Customer::DEFAULT_BRAND]);
        $brand->getNotificationSettings()->setSendTrialEndingWarning(false);
        $this->brandSettingsRepository->getEntityManager()->persist($brand);
        $this->brandSettingsRepository->getEntityManager()->flush();
    }

    #[Then('the trial ending warning email is not sent to the customer')]
    public function theTrialEndingWarningEmailIsNotSentToTheCustomer(): void
    {
        $emails = $this->emailSender->getEmails();

        if (0 === count($emails)) {
            return;
        }

        foreach ($emails as $email) {
            if ('Trial Ending Soon' === $email->getSubject()) {
                throw new \Exception('Found email');
            }
        }
    }
}
