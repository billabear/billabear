<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Vouchers;

use App\Entity\VoucherApplication;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\VoucherApplicationRepository;
use App\Repository\Orm\VoucherRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class ApiContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use VoucherTrait;

    public function __construct(
        private Session $session,
        private VoucherRepository $voucherRepository,
        private VoucherApplicationRepository $voucherApplicationRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @When I apply the voucher code :arg1 to customer :arg2
     */
    public function iApplyTheVoucherCodeToCustomer($code, $customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $this->sendJsonRequest('POST', '/api/v1/customer/'.$customer->getId().'/voucher', ['code' => $code]);
    }

    /**
     * @Then there should be a record of :arg1 being applied to :arg2
     */
    public function thereShouldBeARecordOfBeingAppliedTo($voucherName, $customerEmail)
    {
        $voucher = $this->getVoucher($voucherName);
        $customer = $this->getCustomerByEmail($customerEmail);

        $voucherApplication = $this->voucherApplicationRepository->findOneBy(['customer' => $customer, 'voucher' => $voucher]);

        if (!$voucherApplication instanceof VoucherApplication) {
            throw new \Exception('No application of voucher found');
        }
    }

    /**
     * @Then I should be told there is a validation error with the code
     */
    public function iShouldBeToldThereIsAValidationErrorWithTheCode()
    {
        $data = $this->getJsonContent();
        if (!isset($data['errors'])) {
            throw new \Exception('No errors');
        }

        if (!isset($data['errors']['code'])) {
            throw new \Exception('No error with code');
        }
    }

    /**
     * @Then I should be told there is a validation error with the name
     */
    public function iShouldBeToldThereIsAValidationErrorWithThename()
    {
        $data = $this->getJsonContent();
        if (!isset($data['errors'])) {
            throw new \Exception('No errors');
        }

        if (!isset($data['errors']['name'])) {
            throw new \Exception('No error with name');
        }
    }

    /**
     * @Then I should be told there is a validation error with the URL
     */
    public function iShouldBeToldThereIsAValidationErrorWithTheUrl()
    {
        $data = $this->getJsonContent();
        if (!isset($data['errors'])) {
            throw new \Exception('No errors');
        }

        if (!isset($data['errors']['url'])) {
            throw new \Exception('No error with url');
        }
    }

    /**
     * @Then there should not be a record of :arg1 being applied to :arg2
     */
    public function thereShouldNotBeARecordOfBeingAppliedTo($voucherName, $customerEmail)
    {
        $voucher = $this->getVoucher($voucherName);
        $customer = $this->getCustomerByEmail($customerEmail);

        $voucherApplication = $this->voucherApplicationRepository->findOneBy(['customer' => $customer, 'voucher' => $voucher]);

        if ($voucherApplication instanceof VoucherApplication) {
            throw new \Exception('Application of voucher found');
        }
    }

    /**
     * @Given the customer :arg1 has the voucher :arg2 applied
     */
    public function theCustomerHasTheVoucherApplied($customerEmail, $voucherName)
    {
        $voucher = $this->getVoucher($voucherName);
        $customer = $this->getCustomerByEmail($customerEmail);

        $voucherApplication = new VoucherApplication();
        $voucherApplication->setVoucher($voucher);
        $voucherApplication->setCustomer($customer);
        $voucherApplication->setCreatedAt(new \DateTime());

        $this->voucherApplicationRepository->getEntityManager()->persist($voucherApplication);
        $this->voucherApplicationRepository->getEntityManager()->flush();
    }

    /**
     * @Given the customer :arg1 has the voucher :arg2 applied that has been used
     */
    public function theCustomerHasTheVoucherAppliedThatHasBeenUsed($customerEmail, $voucherName)
    {
        $voucher = $this->getVoucher($voucherName);
        $customer = $this->getCustomerByEmail($customerEmail);

        $voucherApplication = new VoucherApplication();
        $voucherApplication->setVoucher($voucher);
        $voucherApplication->setCustomer($customer);
        $voucherApplication->setCreatedAt(new \DateTime());
        $voucherApplication->setUsed(true);

        $this->voucherApplicationRepository->getEntityManager()->persist($voucherApplication);
        $this->voucherApplicationRepository->getEntityManager()->flush();
    }
}
