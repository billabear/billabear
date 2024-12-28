<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Zendesk;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\CustomerSupport\CustomerRegistration;
use BillaBear\Integrations\CustomerSupport\CustomerServiceInterface;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;
use Zendesk\API\HttpClient as ZendeskAPI;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ZendeskAPI $client,
        private Config $config,
    ) {
    }

    public function register(Customer $customer): CustomerRegistration
    {
        $this->getLogger()->info('Registering customer with Zendesk', ['customer_id' => (string) $customer->getId()]);

        $response = $this->client->users()->createOrUpdate($this->buildData($customer));

        $this->getLogger()->info('Customer registered with Zendesk', ['customer_id' => (string) $customer->getId()]);

        return new CustomerRegistration($response->user->id);
    }

    public function update(Customer $customer): void
    {
        $this->getLogger()->info('Updating customer with Zendesk', ['customer_id' => (string) $customer->getId()]);

        $response = $this->client->users()->update($customer->getCustomerSupportReference(), $this->buildData($customer));

        $this->getLogger()->info('Customer updated with Zendesk', ['customer_id' => (string) $customer->getId()]);
    }

    public function buildData(Customer $customer): array
    {
        return [
            'name' => $customer->getName() ?? $customer->getBillingAddress()->getCompanyName() ?? $customer->getBillingEmail(),
            'email' => $customer->getBillingEmail(),
            'role' => 'end-user',
            'details' => 'This user has been created via BillaBear.',
            'user_fields' => [
                'billing_reference' => $customer->getReference(),
                'billabear_url' => $this->config->getSiteUrl().'/site/customer/view/'.$customer->getId(),
            ],
        ];
    }
}
