<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Freshdesk;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\CustomerSupport\CustomerRegistration;
use BillaBear\Integrations\CustomerSupport\CustomerServiceInterface;
use BillaBear\Integrations\CustomerSupport\Freshdesk\Client\ContactService;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ContactService $client,
        private Config $config,
    ) {
    }

    public function register(Customer $customer): CustomerRegistration
    {
        $contact = $this->client->all(['email' => $customer->getBillingEmail()]);

        if (empty($contact)) {
            $contact = $this->client->create($this->buildContact($customer));
            $contactId = $contact['id'];
        } else {
            $customer->setCustomerSupportReference((string) $contact[0]['id']);
            $contactId = $contact[0]['id'];
            $this->update($customer);
        }

        return new CustomerRegistration((string) $contactId);
    }

    public function update(Customer $customer): void
    {
        $this->client->update($customer->getCustomerSupportReference(), $this->buildContact($customer));
    }

    private function buildContact(Customer $customer): array
    {
        $output = [
            'name' => $customer->getName() ?? $customer->getBillingAddress()->getCompanyName() ?? $customer->getBillingEmail(),
            'email' => $customer->getBillingEmail(),
            'custom_fields' => [
                'billabear_url' => $this->config->getSiteUrl().'/site/customer/view/'.$customer->getId(),
            ],
        ];

        return $output;
    }
}
