<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Payments;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Repository\Orm\PaymentServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PaymentServiceRepository $paymentRepository,
    ) {
    }

    /**
     * @When I view the payment list via the API
     */
    public function iViewThePaymentListViaTheApi()
    {
        $this->sendJsonRequest('GET', '/api/v1/payment');
    }

    /**
     * @Then I will see a payment for :arg1 for :arg2 in the list
     */
    public function iWillSeeAPaymentForForInTheList($customerEmail, $amount)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $payment) {
            if ($payment['amount'] == $amount && $payment['customer']['email'] == $customerEmail) {
                return;
            }
        }

        throw new \Exception("Can't find payment");
    }

    /**
     * @Then I will not see a payment for :arg1 for :arg2 in the list
     */
    public function iWillNotSeeAPaymentForForInTheList($customerEmail, $amount)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $payment) {
            if ($payment['amount'] == $amount && $payment['customer']['email'] == $customerEmail) {
                throw new \Exception('Found payment');
            }
        }
    }

    /**
     * @When I refund :refundAmount the payment for :email for :paymentAmount via API
     */
    public function iRefundThePaymentForForViaApi($email, $refundAmount, $paymentAmount)
    {
        $customer = $this->getCustomerByEmail($email);
        $payload = [
          'amount' => (int) $refundAmount,
          'currency' => null,
        ];
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $paymentAmount]);

        $this->sendJsonRequest('POST', '/api/v1/payment/'.$payment->getId().'/refund', $payload);
    }

    /**
     * @When I view the customer payments via the API for :arg1
     */
    public function iViewTheCustomerPaymentsViaTheApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/payment');
    }
}
