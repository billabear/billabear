<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Accounting\CustomerInterface;
use BillaBear\Integrations\Accounting\CustomerRegistration;
use GuzzleHttp\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Configuration;

class CustomerService implements CustomerInterface
{
    use LoggerAwareTrait;

    private AccountingApi $accountingApi;

    public function __construct(
        private string $tenantId,
        Configuration $config,
        ClientInterface $client,
    ) {
        $this->accountingApi = new AccountingApi($client, $config);
    }

    public function register(Customer $customer): CustomerRegistration
    {
    }

    public function update(Customer $customer): void
    {
        // TODO: Implement update() method.
    }

    public function delete(Customer $customer): void
    {
        // TODO: Implement delete() method.
    }

    public function findCustomer(Customer $customer): CustomerRegistration
    {
    }
}
