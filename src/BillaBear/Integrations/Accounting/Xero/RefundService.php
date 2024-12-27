<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Integrations\Accounting\RefundRegistration;
use BillaBear\Integrations\Accounting\RefundServiceInterface;
use GuzzleHttp\ClientInterface;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Common\LoggerAwareTrait;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Configuration;

class RefundService implements RefundServiceInterface
{
    use LoggerAwareTrait;

    private AccountingApi $accountingApi;

    public function __construct(
        private string $tenantId,
        private string $accountCode,
        Configuration $config,
        ClientInterface $client,
    ) {
        $this->accountingApi = new AccountingApi($client, $config);
    }

    public function register(Refund $refund): RefundRegistration
    {
        // TODO: Implement register() method.
    }
}
