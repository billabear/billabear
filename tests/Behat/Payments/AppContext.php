<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Payments;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\PaymentRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class AppContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PaymentRepository $paymentRepository,
    ) {
    }

    /**
     * @When I refund :refundAmount the payment for :email for :paymentAmount
     */
    public function iRefundThePaymentForForViaApi($email, $refundAmount, $paymentAmount)
    {
        $customer = $this->getCustomerByEmail($email);
        $payload = [
            'amount' => (int) $refundAmount,
        ];
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $paymentAmount]);

        $this->sendJsonRequest('POST', '/app/payment/'.$payment->getId().'/refund', $payload);
    }

    /**
     * @Then I will see payments
     */
    public function iWillSeePayments()
    {
        $data = $this->getJsonContent();

        if (!isset($data['payments'])) {
            throw new \Exception('No payments');
        }

        if (0 === count($data['payments'])) {
            throw new \Exception('No payments');
        }
    }
}
