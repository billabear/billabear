<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\EasyBill;

use BillaBear\Entity\Customer;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\CustomerRegistration;
use BillaBear\Integrations\Accounting\CustomerServiceInterface;
use easybill\SDK\Client;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(private Client $client)
    {
    }

    public function register(Customer $customer): CustomerRegistration
    {
        $body = $this->buildCustomerData($customer);
        $this->getLogger()->info('Registering customer to EasyBill', ['customer_id' => (string) $customer->getId(), 'body' => $body]);
        try {
            $response = $this->client->request('POST', 'customers?type=CUSTOMER', $body);
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to register customer to EasyBill', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register customer to EasyBill', previous: $e);
        }

        $this->getLogger()->info('Customer registered to EasyBill', ['customer_id' => (string) $customer->getId()]);

        return new CustomerRegistration((string) $response['id']);
    }

    public function update(Customer $customer): void
    {
        $this->getLogger()->info('Updating customer to EasyBill', ['customer_id' => (string) $customer->getId()]);
        try {
            $response = $this->client->request('PUT', 'customers/'.$customer->getAccountingReference(), $this->buildCustomerData($customer));
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to update customer to EasyBill', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to update customer to EasyBill', previous: $e);
        }

        $this->getLogger()->info('Customer Updated to EasyBill', ['customer_id' => (string) $customer->getId()]);
    }

    public function delete(Customer $customer): void
    {
        $this->getLogger()->info('Deleting customer from EasyBill', ['customer_id' => (string) $customer->getId()]);
        try {
            $response = $this->client->request('DELETE', 'customers/'.$customer->getAccountingReference(), $this->buildCustomerData($customer));
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to delete customer to EasyBill', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to delete customer to EasyBill', previous: $e);
        }

        $this->getLogger()->info('Customer deleted to EasyBill', ['customer_id' => (string) $customer->getId()]);
    }

    public function findCustomer(Customer $customer): ?CustomerRegistration
    {
        $this->getLogger()->info('Searching for customer in EasyBill', ['customer_id' => (string) $customer->getId()]);
        try {
            $response = $this->client->request('DELETE', 'customers/'.$customer->getAccountingReference(), $this->buildCustomerData($customer));
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to delete customer to EasyBill', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to search for customer in EasyBill', previous: $e);
        }

        if (empty($response['items'])) {
            return null;
        }

        $this->getLogger()->info('Customer found to EasyBill', ['customer_id' => (string) $customer->getId()]);

        return new CustomerRegistration((string) $response['items'][0]['id']);
    }

    private function buildCustomerData(Customer $customer): array
    {
        $body = [
            'last_name' => $customer->getDisplayName(),
            'company_name' => $customer->getBillingAddress()->getCompanyName(),
            'street' => $customer->getBillingAddress()->getStreetLineOne(),
            'city' => $customer->getBillingAddress()->getCity(),
            'state' => $customer->getBillingAddress()->getRegion(),
            'country' => $customer->getBillingAddress()->getCountry(),
            'zip_code' => $customer->getBillingAddress()->getPostcode(),
            'vat_identifier' => $customer->getTaxNumber(),
            'personal' => !$customer->isBusiness(),
            'email' => [
                $customer->getBillingEmail(),
            ],
        ];

        if (empty($body['country'])) {
            $body['country'] = $customer->getBrandSettings()->getAddress()->getCountry();
        }

        return $body;
    }
}
