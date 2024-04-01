<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Refund;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Repository\Orm\RefundServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private RefundServiceRepository $refundRepository,
    ) {
    }

    /**
     * @When I view the refund list via API
     */
    public function iViewTheRefundListViaApi()
    {
        $this->sendJsonRequest('GET', '/api/v1/refund');
    }

    /**
     * @Then I will see a refund for :arg1 for :arg2 in the list
     */
    public function iWillSeeARefundForForInTheList($customerEmail, $amount)
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
     * @Then I will not see a refund for :arg1 for :arg2 in the list
     */
    public function iWillNotSeeARefundForForInTheList($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $refund) {
            if ($refund['customer']['email'] === $customerEmail && $refund['amount'] == $amount) {
                throw new \Exception('Found refund');
            }
        }
    }

    /**
     * @When I view the full refund for a payment for :arg1 for :arg2 via API
     */
    public function iViewTheFullRefundForAPaymentForForViaApi($email, $amount)
    {
        $customer = $this->getCustomerByEmail($email);

        $refund = $this->refundRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        if (!$refund instanceof Refund) {
            throw new \Exception("Can't find refund");
        }

        $this->sendJsonRequest('GET', '/api/v1/refund/'.(string) $refund->getId());
    }

    /**
     * @Then I will see the refund api response has the amount of :arg1
     */
    public function iWillSeeTheRefundApiResponseHasTheAmountOf($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['amount'] != $arg1) {
            throw new \Exception("Can't match");
        }
    }

    /**
     * @When I view the customer refund via API for :arg1
     */
    public function iViewTheCustomerRefundViaApiFor($email)
    {
        $customer = $this->getCustomerByEmail($email);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.(string) $customer->getId().'/refund');
    }
}
