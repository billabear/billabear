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

namespace App\Tests\Behat\Payments;

use App\Entity\PaymentAttempt;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PaymentAttemptRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;
use Parthenon\Billing\Repository\Orm\PaymentServiceRepository;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionPlanServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionServiceRepository;

class MainContext implements Context
{
    use SubscriptionTrait;
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private SubscriptionServiceRepository $subscriptionRepository,
        private PriceServiceRepository $priceRepository,
        private SubscriptionPlanServiceRepository $planRepository,
        private CustomerRepository $customerRepository,
        private PaymentCardServiceRepository $paymentDetailsRepository,
        private PaymentServiceRepository $paymentRepository,
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
