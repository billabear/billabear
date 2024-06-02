<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\ChargeBacks;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\PaymentRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Enum\ChargeBackReason;
use Parthenon\Billing\Enum\ChargeBackStatus;
use Parthenon\Billing\Repository\Orm\ChargeBackServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private PaymentRepository $paymentRepository,
        private ChargeBackServiceRepository $chargeBackRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @When there is a charge back for the payment for :arg1 for :arg2
     */
    public function thereIsAChargeBackForThePaymentForFor($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $payment = $this->paymentRepository->findOneBy(['customer' => $customer, 'amount' => $amount]);

        $chargeBack = new ChargeBack();
        $chargeBack->setPayment($payment);
        $chargeBack->setCustomer($customer);
        $chargeBack->setStatus(ChargeBackStatus::NEED_RESPONSE);
        $chargeBack->setReason(ChargeBackReason::FRAUDULENT);
        $chargeBack->setCreatedAt(new \DateTime('now'));
        $chargeBack->setUpdatedAt(new \DateTime());
        $chargeBack->setExternalReference(bin2hex(random_bytes(32)));

        $this->chargeBackRepository->getEntityManager()->persist($chargeBack);
        $this->chargeBackRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the charge back list
     */
    public function iViewTheChargeBackList()
    {
        $this->sendJsonRequest('GET', '/app/charge-backs');
    }

    /**
     * @Then I will see a charge back for payment for :arg1 for :arg2
     */
    public function iWillSeeAChargeBackForPaymentForFor($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $chargeBack) {
            if ($chargeBack['customer']['email'] === $arg1 && $chargeBack['payment']['amount'] == $arg2) {
                return;
            }
        }

        throw new \Exception('Not found');
    }
}
