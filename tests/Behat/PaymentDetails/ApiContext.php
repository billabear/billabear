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

namespace App\Tests\Behat\PaymentDetails;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Repository\Orm\PaymentCardServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use PaymentDetailsTrait;

    public function __construct(
        private Session $session,
        private PaymentCardServiceRepository $paymentDetailsRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * @When I make the payment methods :arg1 for :arg2 default
     */
    public function iMakeThePaymentDetailsForDefault($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('POST', '/api/v1/customer/'.$customer->getId().'/payment-methods/'.$paymentDetails->getId().'/default');
    }

    /**
     * @When I delete the payment methods :arg1 for :arg2
     */
    public function iDeleteThePaymentDetailsFor($name, $email)
    {
        $customer = $this->getCustomerByEmail($email);
        $paymentDetails = $this->findPaymentMethod($customer, $name);

        $this->sendJsonRequest('DELETE', '/api/v1/customer/'.$customer->getId().'/payment-methods/'.$paymentDetails->getId());
    }
}
