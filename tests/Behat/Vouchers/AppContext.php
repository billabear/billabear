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

namespace App\Tests\Behat\Vouchers;

use App\Entity\Voucher;
use App\Enum\VoucherEntryType;
use App\Enum\VoucherEvent;
use App\Enum\VoucherType;
use App\Repository\Orm\VoucherRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class AppContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private VoucherRepository $voucherRepository,
    ) {
    }

    /**
     * @When I create a voucher:
     */
    public function iCreateAVoucher(TableNode $table)
    {
        $data = $table->getRowsHash();

        $automaticEvent = match ($data['Entry Event'] ?? null) {
            'Expired Card Warning' => VoucherEvent::EXPIRED_CARD_ADDED->value,
            default => null,
        };

        $prices = [];

        if (isset($data['USD'])) {
            $prices[] = [
                'currency' => 'USD',
                'amount' => (int) $data['USD'],
            ];
        }
        if (isset($data['GBP'])) {
            $prices[] = [
                'currency' => 'GBP',
                'amount' => (int) $data['GBP'],
            ];
        }

        $payload = [
            'type' => ('percentage' == strtolower($data['Type'])) ? VoucherType::PERCENTAGE->value : VoucherType::FIXED_CREDIT->value,
            'entry_type' => ('manual' === strtolower($data['Entry Type'])) ? VoucherEntryType::MANUAL->value : VoucherEntryType::AUTOMATIC->value,
            'entry_event' => $automaticEvent,
            'value' => $data['Value'] ?? null,
            'name' => $data['Name'],
            'amounts' => $prices,
            'code' => $data['Code'] ?? null,
        ];

        $this->sendJsonRequest('POST', '/app/voucher', $payload);
    }

    /**
     * @Then there should be a voucher called :arg1 with:
     */
    public function thereShouldBeAVoucherCalledWith($voucherName, TableNode $table)
    {
        $voucher = $this->getVoucher($voucherName);

        $data = $table->getRowsHash();

        if (isset($data['Value'])) {
            if ($data['Value'] != $voucher->getValue()) {
                throw new \Exception('Different value found');
            }
        }

        $type = ('percentage' == strtolower($data['Type'])) ? VoucherType::PERCENTAGE : VoucherType::FIXED_CREDIT;

        if ($type !== $voucher->getType()) {
            throw new \Exception('Different type');
        }
    }

    /**
     * @Then there should not be a voucher called :arg1
     */
    public function thereShouldNotBeAVoucherCalled($voucherName)
    {
        try {
            $voucher = $this->getVoucher($voucherName);
        } catch (\Exception $E) {
            return;
        }

        throw new \Exception('Voucher found');
    }

    /**
     * @Then there will be a validation error for the amounts
     */
    public function thereWillBeAValidationErrorForTheAmounts()
    {
        $data = $this->getJsonContent();

        if (!isset($data['errors']['amounts'])) {
            throw new \Exception("Can't see validation error");
        }
    }

    /**
     * @Then the voucher :arg1 has an amount for the currency :arg2 and value :arg3
     */
    public function theVoucherHasAnAmountForTheCurrencyAndValue($voucherName, $currency, $value)
    {
        $voucher = $this->getVoucher($voucherName);

        foreach ($voucher->getAmounts() as $amount) {
            if ($amount->getCurrency() == $currency && $amount->getAmount() == $value) {
                return;
            }
        }

        throw new \Exception("Can't find amount");
    }

    /**
     * @throws \Exception
     */
    public function getVoucher($voucherName): Voucher
    {
        $voucher = $this->voucherRepository->findOneBy(['name' => $voucherName]);

        if (!$voucher instanceof Voucher) {
            throw new \Exception('No voucher found');
        }

        $this->voucherRepository->getEntityManager()->refresh($voucher);

        return $voucher;
    }

    /**
     * @Then there should be a voucher called :arg1
     */
    public function thereShouldBeAVoucherCalled($voucherName)
    {
        $this->getVoucher($voucherName);
    }

    /**
     * @Then the voucher :arg1 has the code :arg2
     */
    public function theVoucherHasTheCode($voucherName, $code)
    {
        $voucher = $this->getVoucher($voucherName);

        if ($voucher->getCode() !== $code) {
            throw new \Exception('The Code is '.$voucher->getCode());
        }
    }

    /**
     * @Then there should be a validation error for code
     */
    public function thereShouldBeAValidationErrorForCode()
    {
        $data = $this->getJsonContent();

        if (!isset($data['errors']['code'])) {
            throw new \Exception("Can't see validation error");
        }
    }
}
