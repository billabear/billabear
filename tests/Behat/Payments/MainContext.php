<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Payments;

use App\Entity\PaymentAttempt;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PaymentAttemptRepository;
use App\Repository\Orm\PaymentRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\SubscriptionRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class MainContext implements Context
{
    use SubscriptionTrait;
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SubscriptionRepository $subscriptionRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planRepository,
        private CustomerRepository $customerRepository,
        private PaymentCardServiceRepository $paymentDetailsRepository,
        private PaymentRepository $paymentRepository,
        private PaymentAttemptRepository $paymentAttemptRepository,
    ) {
    }

    /**
     * @When there is a payments for:
     */
    public function thereIsAPaymentsFor(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $payment = new Payment();
            if (!empty($row['Customer'])) {
                $subscription = $this->getSubscription($row['Customer'], $row['Subscription Plan']);

                $customer = $this->getCustomerByEmail($row['Customer']);
                $payment->setCustomer($customer);
                $payment->addSubscription($subscription);
            }

            $payment->setStatus(PaymentStatus::COMPLETED);
            $payment->setCreatedAt(new \DateTime('now'));
            $payment->setUpdatedAt(new \DateTime('now'));
            $payment->setAmount((int) $row['Amount']);
            $payment->setCurrency($row['Currency'] ?? 'USD');
            $payment->setProvider('dummy_test');
            $payment->setPaymentReference(bin2hex(random_bytes(32)));

            $this->paymentRepository->getEntityManager()->persist($payment);
            $this->paymentRepository->getEntityManager()->flush();
        }
    }

    /**
     * @When I view the payment list
     */
    public function iViewThePaymentList()
    {
        $this->sendJsonRequest('GET', '/app/payments');
    }

    /**
     * @When I attach the payment for :arg3 :arg1 to :arg2
     */
    public function iAttachThePaymentForTo($amount, $currency, $customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $payment = $this->paymentRepository->findOneBy(['amount' => $amount, 'currency' => $currency]);

        $this->sendJsonRequest('POST', '/app/payment/'.$payment->getId().'/attach', ['customer' => (string) $customer->getId()]);
    }

    /**
     * @Then the payment for :arg3 :arg1 should belong to :arg2
     */
    public function thePaymentForShouldBelongTo($amount, $currency, $customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Payment $payment */
        $payment = $this->paymentRepository->findOneBy(['amount' => $amount, 'currency' => $currency]);
        $this->paymentRepository->getEntityManager()->refresh($payment);

        if ($customer->getId() != $payment->getCustomer()?->getId()) {
            throw new \Exception('Wrong customer');
        }
    }

    /**
     * @Then I will see a payment for :arg1 for :arg2
     */
    public function iWillSeeAPaymentForFor($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $payment) {
            if (!isset($payment['customer'])) {
                continue;
            }
            if ($payment['amount'] == $arg2 && $payment['customer']['email'] == $arg1) {
                return;
            }
        }

        throw new \Exception("Can't find payment");
    }

    /**
     * @When I view the payment for :arg1 for :arg2
     */
    public function iViewThePaymentForFor($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        $this->sendJsonRequest('GET', '/app/payments/'.$payment->getId());
    }

    /**
     * @Then I will see the details for the payment
     */
    public function iWillSeeTheDetailsForThePayment()
    {
        $data = $this->getJsonContent();

        if (!isset($data['payment'])) {
            throw new \Exception("Can't see any payment info");
        }
    }

    /**
     * @Then there is a payment attempt for :arg1 will exist
     */
    public function thereIsAPaymentAttemptForWillExist($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $paymentAttempt = $this->paymentAttemptRepository->findOneBy(['customer' => $customer]);

        if (!$paymentAttempt instanceof PaymentAttempt) {
            throw new \Exception('No payment attempt found');
        }
    }
}
