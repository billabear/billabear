<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Crm\CustomerProfile;
use BillaBear\Integrations\Crm\CustomerRegistration;
use BillaBear\Integrations\Crm\CustomerServiceInterface;
use HubSpot\Client\Crm\Companies\ApiException;
use HubSpot\Client\Crm\Companies\Model\Filter;
use HubSpot\Client\Crm\Companies\Model\FilterGroup;
use HubSpot\Client\Crm\Companies\Model\PublicObjectSearchRequest;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObject;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInput;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInputForCreate;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectWithAssociations;
use HubSpot\Discovery\Discovery;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public const array COMPANY_PROPERTIES = [
        'name',
        'city',
        'state',
        'zip',
        'country',
        'hs_object_id',
    ];

    public function __construct(private Discovery $client)
    {
    }

    public function register(Customer $customer): CustomerRegistration
    {
        $this->getLogger()->info('Registering customer to Hubspot', ['customer_id' => (string) $customer->getId()]);
        $companyInput = new SimplePublicObjectInputForCreate();
        $companyInput->setProperties(
            [
                'name' => $customer->getBillingAddress()->getCompanyName(),
                'city' => $customer->getBillingAddress()->getCity(),
                'state' => $customer->getBillingAddress()->getRegion(),
                'zip' => $customer->getBillingAddress()->getPostCode(),
                'country' => $customer->getBillingAddress()->getCountry(),
            ]
        );
        try {
            $company = $this->client->crm()->companies()->basicApi()->create($companyInput);
        } catch (ApiException $e) {
            var_dump($e->getResponseBody());
            exit;
            $this->getLogger()->error('Failed to register customer to Hubspot', ['customer_id' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }

        return new CustomerRegistration($company->getId());
    }

    public function update(Customer $customer): void
    {
        $this->getLogger()->info('Updating customer to Hubspot', ['customer_id' => (string) $customer->getId()]);
        $companyInput = new SimplePublicObjectInput();
        $companyInput->setProperties(
            [
                'name' => $customer->getBillingAddress()->getCompanyName(),
                'city' => $customer->getBillingAddress()->getCity(),
                'state' => $customer->getBillingAddress()->getRegion(),
                'zip' => $customer->getBillingAddress()->getPostCode(),
                'country' => $customer->getBillingAddress()->getCountry(),
            ]
        );

        $this->client->crm()->companies()->basicApi()->update($customer->getCrmReference(), $companyInput);
    }

    public function search(Customer $customer): ?CustomerProfile
    {
        $this->getLogger()->info('Searching Hubspot for customer', ['customer_id' => (string) $customer->getId()]);
        $company = $this->findCompanyByName($customer->getBillingAddress()->getCompanyName());
        if (null === $company) {
            $company = $this->findCompanyByContact($customer);
        }
        if (null === $company) {
            return null;
        }

        $properties = $company->getProperties();

        return new CustomerProfile(
            $company->getId(),
            $properties['name'],
            $properties['city'],
            $properties['state'],
            $properties['zip'],
            $properties['country'],
        );
    }

    private function findCompanyByName(?string $name): ?SimplePublicObject
    {
        if (empty($name)) {
            return null;
        }

        $hubspot = $this->client;
        $filter = new Filter();
        $filter
            ->setPropertyName('name')
            ->setOperator('EQ')
            ->setValue($name);

        $filterGroup = new FilterGroup();
        $filterGroup->setFilters([$filter]);

        $searchRequest = new PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([$filterGroup]);
        $searchRequest->setProperties(self::COMPANY_PROPERTIES);

        $companiesResponse = $hubspot->crm()->companies()->searchApi()->doSearch($searchRequest);
        $companies = $companiesResponse->getResults();

        if (empty($companies)) {
            return null;
        }

        return $companies[0];
    }

    private function findCompanyByContact(Customer $customer): ?SimplePublicObjectWithAssociations
    {
        $email = $customer->getBillingEmail();
        $hubspot = $this->client;
        $filter = new \HubSpot\Client\Crm\Contacts\Model\Filter();
        $filter
            ->setPropertyName('email')
            ->setOperator('EQ')
            ->setValue($email);

        $filterGroup = new \HubSpot\Client\Crm\Contacts\Model\FilterGroup();
        $filterGroup->setFilters([$filter]);

        $searchRequest = new \HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest();
        $searchRequest->setFilterGroups([$filterGroup]);
        $searchRequest->setProperties(['name', 'address', 'city', 'state', 'zip', 'country', 'hs_object_id']);

        $contactsResponse = $hubspot->crm()->contacts()->searchApi()->doSearch($searchRequest);
        $contacts = $contactsResponse->getResults();

        if (empty($contacts)) {
            return null;
        }

        $contactId = $contacts[0]->getId();
        $associations = $hubspot->crm()->associations()->v4()->basicApi()->getPage(
            'contacts',  // From object type
            $contactId,  // From object ID
            'companies', // To object type
            ['limit' => 1]
        );

        if (empty($associations->getResults())) {
            return null;
        }

        return $hubspot->crm()->companies()->basicApi()->getById($associations->getResults()[0]->getToObjectId(), self::COMPANY_PROPERTIES);
    }
}
