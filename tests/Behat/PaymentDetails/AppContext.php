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
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Repository\Orm\PaymentDetailsServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private PaymentDetailsServiceRepository $paymentDetailsRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * @When the following payment details:
     */
    public function theFollowingPaymentDetails(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $customer = $this->getCustomerByEmail($row['Customer Email']);
            $paymentDetails = new PaymentDetails();
            $paymentDetails->setName($row['Name'] ?? 'One');
            $paymentDetails->setBrand($row['Brand'] ?? 'dummy');
            $paymentDetails->setLastFour($row['Last Four']);
            $paymentDetails->setExpiryMonth($row['Expiry Month']);
            $paymentDetails->setExpiryYear($row['Expiry Year']);
            $paymentDetails->setStoredPaymentReference(bin2hex(random_bytes(32)));
            $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
            $paymentDetails->setCustomer($customer);
            $paymentDetails->setCreatedAt(new \DateTime());
            $paymentDetails->setDefaultPaymentOption(true);

            $this->paymentDetailsRepository->getEntityManager()->persist($paymentDetails);
        }

        $this->paymentDetailsRepository->getEntityManager()->flush();
    }

    /**
     * @Then I will see :arg1 payment details
     */
    public function iWillSeePaymentDetails($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['payment_details'])) {
            throw new \Exception('No payment details found');
        }

        if (count($data['payment_details']) != $arg1) {
            throw new \Exception('Wrong count');
        }
    }
}
