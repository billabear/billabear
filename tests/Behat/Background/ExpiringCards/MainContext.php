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

namespace App\Tests\Behat\Background\ExpiringCards;

use App\Background\ExpiringCards\DayBefore;
use App\Background\ExpiringCards\StartProcess;
use App\Entity\Processes\ExpiringCardProcess;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\ExpiringCardProcessRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use Behat\Behat\Context\Context;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class MainContext implements Context
{
    use CustomerTrait;

    public function __construct(
        private StartProcess $startProcess,
        private DayBefore $beforeNextCharge,
        private ExpiringCardProcessRepository $expiringCardProcessRepository,
        private PaymentCardServiceRepository $paymentCardRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @When the background task for sending first card expiry warning
     */
    public function theBackgroundTaskForSendingFirstCardExpiryWarning()
    {
        $this->startProcess->execute();
    }

    /**
     * @Then the workflow for expired card will exist for :arg1
     */
    public function theWorkflowForExpiredCardWillExistFor($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }
    }

    /**
     * @Then the workflow for expired card will not exist for :arg1
     */
    public function theWorkflowForExpiredCardWillNotExistFor($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);

        if ($process instanceof ExpiringCardProcess) {
            throw new \Exception('Found process');
        }
    }

    /**
     * @Then the workflow for expired card for :arg1 will be that an email had been sent
     */
    public function theWorkflowForExpiredCardForWillBeThatAnEmailHadBeenSent($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }

        if ('first_email_sent' !== $process->getState()) {
            throw new \Exception('Process is at '.$process->getState());
        }
    }

    /**
     * @Given there are expiring card process for :arg1 for card :arg2 has sent the first email
     */
    public function thereAreExpiringCardProcessForForCardHasSentTheFirstEmail($customerEmail, $lastFour)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $paymentCard = $this->paymentCardRepository->findOneBy(['customer' => $customer, 'lastFour' => $lastFour]);

        $process = new ExpiringCardProcess();
        $process->setCustomer($customer);
        $process->setPaymentCard($paymentCard);
        $process->setState('first_email_sent');
        $process->setCreatedAt(new \DateTime('-4 days'));
        $process->setUpdatedAt(new \DateTime('-3 days'));
        $process->setSubscriptionChargedAt(new \DateTime('-2 days'));

        $this->expiringCardProcessRepository->getEntityManager()->persist($process);
        $this->expiringCardProcessRepository->getEntityManager()->flush();
    }

    /**
     * @When the background task for day before next charge for expiring cards is ran
     */
    public function theBackgroundTaskForDayBeforeNextChargeForExpiringCardsIsRan()
    {
        $this->beforeNextCharge->execute();
    }

    /**
     * @Then the process for expired card for :arg1 will be that a day before valid email was sent
     */
    public function theProcessForExpiredCardForWillBeThatADayBeforeValidEmailWasSent($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);
        $this->expiringCardProcessRepository->getEntityManager()->refresh($process);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }

        if ('day_before_valid_email_sent' !== $process->getState()) {
            throw new \Exception('Process is at '.$process->getState());
        }
    }

    /**
     * @Then the process for expired card for :arg1 will be that a day before valid email was not sent
     */
    public function theProcessForExpiredCardForWillBeThatADayBeforeValidEmailWasNotSent($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);
        $this->expiringCardProcessRepository->getEntityManager()->refresh($process);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }

        if ('first_email_sent' !== $process->getState()) {
            throw new \Exception('Process is at '.$process->getState());
        }
    }

    /**
     * @Then the process for expired card for :arg1 will be that a day before no longer valid email was sent
     */
    public function theProcessForExpiredCardForWillBeThatADayBeforeNoLongerValidEmailWasSent($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);
        $this->expiringCardProcessRepository->getEntityManager()->refresh($process);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }

        if ('day_before_not_valid_email_sent' !== $process->getState()) {
            throw new \Exception('Process is at '.$process->getState());
        }
    }
}
