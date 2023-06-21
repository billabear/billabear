<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Refund;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Enum\RefundStatus;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;
use Parthenon\Billing\Repository\Orm\PaymentServiceRepository;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\RefundServiceRepository;
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
        private RefundServiceRepository $refundRepository,
    ) {
    }

    /**
     * @When there is a full refund for a payment for :arg1 for :arg2
     */
    public function thereIsAFullRefundForAPaymentForFor($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        /** @var Payment $payment */
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        $refund = new Refund();
        $refund->setPayment($payment);
        $refund->setCustomer($customer);
        $refund->setCurrency($payment->getCurrency());
        $refund->setAmount($payment->getAmount());
        $refund->setStatus(RefundStatus::ISSUED);
        $refund->setExternalReference(bin2hex(random_bytes(32)));
        $refund->setCreatedAt(new \DateTime());

        $this->refundRepository->getEntityManager()->persist($refund);
        $this->refundRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the refund list
     */
    public function iViewTheRefundList()
    {
        $this->sendJsonRequest('GET', '/app/refund');
    }

    /**
     * @Then I will see a refund for :arg1 for :arg2
     */
    public function iWillSeeARefundForFor($customerEmail, $amount)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $refund) {
            if ($refund['customer']['email'] === $customerEmail && $refund['amount'] == $amount) {
                return;
            }
        }

        throw new \Exception('No such refund');
    }

    /**
     * @Then there will be a refund for :arg1 of :arg2
     */
    public function thereWillBeARefundForOf($email, $amount)
    {
        $customer = $this->getCustomerByEmail($email);
        $refund = $this->refundRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        if (!$refund instanceof Refund) {
            throw new \Exception('Refund not found');
        }
    }

    /**
     * @When I view the full refund for a payment for :arg1 for :arg2 via APP
     */
    public function iViewTheFullRefundForAPaymentForForViaApp($email, $amount)
    {
        $customer = $this->getCustomerByEmail($email);

        $refund = $this->refundRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        if (!$refund instanceof Refund) {
            throw new \Exception("Can't find refund");
        }

        $this->sendJsonRequest('GET', '/app/refund/'.(string) $refund->getId());
    }

    /**
     * @Then I will see the refund response has the amount of :arg1
     */
    public function iWillSeeTheRefundResponseHasTheAmountOf($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['refund']['amount'])) {
            throw new \Exception('Refund not set');
        }

        if ($data['refund']['amount'] != $arg1) {
            throw new \Exception('Refund not the same amount');
        }
    }

    /**
     * @Then I will see refunds
     */
    public function iWillSeeRefunds()
    {
        $data = $this->getJsonContent();

        if (!isset($data['refunds'])) {
            throw new \Exception('No refunds');
        }

        if (0 === count($data['refunds'])) {
            throw new \Exception('No refunds');
        }
    }
}
