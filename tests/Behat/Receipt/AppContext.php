<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Receipt;

use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PaymentRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Repository\Orm\ReceiptServiceRepository;

class AppContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PaymentRepository $paymentRepository,
        private ReceiptServiceRepository $receiptRepository
    ) {
    }

    /**
     * @When I generate a receipt for the payment for :arg1 for :arg2
     */
    public function iGenerateAReceiptForThePaymentForFor($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        $this->sendJsonRequest('POST', '/app/payment/'.$payment->getId().'/generate-receipt');
    }

    /**
     * @Then then there will be a receipt for :arg1 for :arg2
     */
    public function thenThereWillBeAReceiptForFor($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $receipt = $this->receiptRepository->findOneBy(['customer' => $customer, 'total' => $amount]);
        if (!$receipt instanceof Receipt) {
            throw new \Exception('No receipt found');
        }
    }
}
