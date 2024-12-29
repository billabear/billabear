<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\EmailOctopus;

use BillaBear\Entity\Customer;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Newsletter\CustomerRegistration;
use BillaBear\Integrations\Newsletter\CustomerServiceInterface;
use GoranPopovic\EmailOctopus\Client;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ?string $listId,
        private Client $client,
    ) {
    }

    public function register(Customer $customer): CustomerRegistration
    {
        $this->getLogger()->info('Registering customer to EmailOctopus', ['customer_id' => (string) $customer->getId()]);

        try {
            $response = $this->client->lists()->createContact($this->listId,
                [
                    'email_address' => $customer->getBillingEmail(),
                    'status' => $customer->getMarketingOptIn() ? 'SUBSCRIBED' : 'UNSUBSCRIBED',
                ]
            );
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to register customer to EmailOctopus', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register customer to EmailOctopus', previous: $e);
        }

        $this->getLogger()->info('Registered customer to EmailOctopus', ['customer_id' => (string) $customer->getId()]);

        return new CustomerRegistration($response['id']);
    }

    public function update(Customer $customer): void
    {
        $this->getLogger()->info('Updating customer to EmailOctopus', ['customer_id' => (string) $customer->getId()]);

        try {
            $this->client->lists()->updateContact($this->listId, $customer->getNewsletterReference(),
                [
                    'email_address' => $customer->getBillingEmail(),
                    'status' => $customer->getMarketingOptIn() ? 'SUBSCRIBED' : 'UNSUBSCRIBED',
                ]
            );
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to update customer to EmailOctopus', ['customer_id' => (string) $customer->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to update customer to EmailOctopus', previous: $e);
        }
        $this->getLogger()->info('Updating customer to EmailOctopus', ['customer_id' => (string) $customer->getId()]);
    }
}
