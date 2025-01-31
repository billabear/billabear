<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\Mailchimp;

use BillaBear\Entity\Customer;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Newsletter\CustomerRegistration;
use BillaBear\Integrations\Newsletter\CustomerServiceInterface;
use GuzzleHttp\Exception\RequestException;
use MailchimpMarketing\ApiClient;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(private ApiClient $client)
    {
    }

    public function register(string $listId, Customer $customer): CustomerRegistration
    {
        $this->getLogger()->info('Registering customer to Mailchimp list', ['listId' => $listId, 'customer' => $customer]);

        try {
            $response = $this->client->lists->addListMember($listId, [
                'email_address' => $customer->getBillingEmail(),
                'status' => 'subscribed',
            ]);
        } catch (RequestException $e) {
            throw new UnexpectedErrorException('Failed to register customer to Mailchimp list', previous: $e);
        }
        $this->getLogger()->info('Registering customer to Mailchimp list', ['listId' => $listId, 'customer' => $customer]);

        return new CustomerRegistration($response->id);
    }

    public function update(string $listId, string $reference, bool $subscribe, Customer $customer): void
    {
        $this->getLogger()->info('Updating customer to Mailchimp list', ['listId' => $listId, 'customer' => $customer]);

        $this->client->lists->updateListMember($listId, $reference, [
            'email_address' => $customer->getBillingEmail(),
            'status' => $subscribe ? 'subscribed' : 'unsubscribed',
        ]);
        $this->getLogger()->info('Updating customer to Mailchimp list', ['listId' => $listId, 'customer' => $customer]);
    }

    public function isSubscribed(string $listId, string $reference): bool
    {
        $this->getLogger()->info('Checking if customer is subscribed to Mailchimp list', ['listId' => $listId, 'reference' => $reference]);

        $response = $this->client->lists->getListMember($listId, $reference);

        return 'subscribed' === strtolower($response->status);
    }
}
