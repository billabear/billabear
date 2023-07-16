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

namespace App\Tests\Behat\ChargeBacks;

use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PaymentRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
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
