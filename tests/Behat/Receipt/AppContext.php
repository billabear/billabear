<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Receipt;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\PaymentRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
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
