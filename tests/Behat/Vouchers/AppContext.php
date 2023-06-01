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

        $automaticEvent = match ($data['Automatic Event'] ?? null) {
            'Expired Card Warning' => VoucherEvent::EXPIRED_CARD_ADDED->value,
            default => null,
        };

        $payload = [
            'type' => ('Percentage' == strtolower($data['Type'])) ? VoucherType::PERCENTAGE->value : VoucherType::FIXED_CREDIT->value,
            'entry_type' => ('manual' === strtolower($data['Entry Type'])) ? VoucherEntryType::MANUAL->value : VoucherEntryType::AUTOMATIC->value,
            'automatic_event' => $automaticEvent,
            'value' => $data['Value'],
            'name' => $data['Name'],
            ];

        $this->sendJsonRequest('POST', '/app/voucher', $payload);
    }

    /**
     * @Then there should be a voucher called :arg1
     */
    public function thereShouldBeAVoucherCalled($voucherName)
    {
        $voucher = $this->voucherRepository->findOneBy(['name' => $voucherName]);

        if (!$voucher instanceof Voucher) {
            throw new \Exception('No voucher found');
        }
    }
}
