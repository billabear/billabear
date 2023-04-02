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

namespace App\Tests\Behat\PaymentDetails;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Repository\Orm\PaymentDetailsServiceRepository;

class DefaultContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use PaymentDetailsTrait;

    public function __construct(
        private Session $session,
        private PaymentDetailsServiceRepository $paymentDetailsRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * @Then the payment details :arg1 for :arg2 should be deleted
     */
    public function thePaymentDetailsForShouldBeDeleted($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentDetails($customer, $name);

        if (!$paymentDetails->isDeleted()) {
            throw new \Exception('Is not deleted');
        }
    }

    /**
     * @Then the payment details :arg1 for :arg2 should be default
     */
    public function thePaymentDetailsShouldBeDefault($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentDetails($customer, $name);

        if (!$paymentDetails->isDefaultPaymentOption()) {
            throw new \Exception('Is not default');
        }
    }

    /**
     * @Then the payment details :arg1 for :arg2 should not be default
     */
    public function thePaymentDetailsCardTwoShouldNotBeDefault($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentDetails($customer, $name);

        if ($paymentDetails->isDefaultPaymentOption()) {
            throw new \Exception('Is default');
        }
    }
}
