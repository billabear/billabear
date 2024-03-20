<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Background\ExpiringCards;

use App\Background\ExpiringCards\DayBefore;
use App\Background\ExpiringCards\StartProcess;
use App\Entity\Processes\ExpiringCardProcess;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\ExpiringCardProcessRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use Behat\Behat\Context\Context;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Event\PaymentCardAdded;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MainContext implements Context
{
    use CustomerTrait;

    public function __construct(
        private StartProcess $startProcess,
        private DayBefore $beforeNextCharge,
        private ExpiringCardProcessRepository $expiringCardProcessRepository,
        private PaymentCardServiceRepository $paymentCardRepository,
        private CustomerRepository $customerRepository,
        private EventDispatcherInterface $eventDispatcher,
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
     * @When a new payment card is added for :arg1
     */
    public function aNewPaymentCardIsAddedFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $paymentCard = new PaymentCard();
        $paymentCard->setCustomer($customer);
        $paymentCard->setLastFour(random_int(1000, 9999));
        $paymentCard->setExpiryMonth(12);
        $paymentCard->setExpiryYear(34);
        $paymentCard->setBrand('dummy');
        $paymentCard->setProvider('stripe');
        $paymentCard->setDefaultPaymentOption(true);
        $paymentCard->setCreatedAt(new \DateTime());

        $this->paymentCardRepository->getEntityManager()->persist($paymentCard);
        $this->paymentCardRepository->getEntityManager()->flush();

        $this->eventDispatcher->dispatch(new PaymentCardAdded($customer, $paymentCard), PaymentCardAdded::NAME);
    }

    /**
     * @Then the expiring card process for :arg1 will be terminated at the card_added
     */
    public function theExpiringCardProcessForWillBeTerminatedAtTheCardAdded($customerEmail)
    {
        $process = $this->getProcess($customerEmail);

        if ('card_added' !== $process->getState()) {
            throw new \Exception('State is not card_added');
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
        $process = $this->getProcess($email);

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
        $process = $this->getProcess($email);

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
        $process = $this->getProcess($email);

        if (!$process instanceof ExpiringCardProcess) {
            throw new \Exception("Can't find process");
        }

        if ('day_before_not_valid_email_sent' !== $process->getState()) {
            throw new \Exception('Process is at '.$process->getState());
        }
    }

    /**
     * @return object|null
     *
     * @throws \Exception
     */
    public function getProcess($email): ?ExpiringCardProcess
    {
        $customer = $this->getCustomerByEmail($email);
        $process = $this->expiringCardProcessRepository->findOneBy(['customer' => $customer]);
        $this->expiringCardProcessRepository->getEntityManager()->refresh($process);

        return $process;
    }
}
