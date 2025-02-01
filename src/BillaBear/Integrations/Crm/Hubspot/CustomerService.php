<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Crm\ContactRegistration;
use BillaBear\Integrations\Crm\CustomerProfile;
use BillaBear\Integrations\Crm\CustomerRegistration;
use BillaBear\Integrations\Crm\CustomerServiceInterface;
use HubSpot\Client\Crm\Companies\ApiException;
use HubSpot\Client\Crm\Companies\Model\Filter;
use HubSpot\Client\Crm\Companies\Model\FilterGroup;
use HubSpot\Client\Crm\Companies\Model\PublicObjectSearchRequest;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObject;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInput;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectInputForCreate as CompanyCreate;
use HubSpot\Client\Crm\Companies\Model\SimplePublicObjectWithAssociations;
use HubSpot\Client\Crm\Contacts\ApiException as ContactApiException;
use HubSpot\Client\Crm\Contacts\Model\AssociationSpec;
use HubSpot\Client\Crm\Contacts\Model\Filter as ContactFilter;
use HubSpot\Client\Crm\Contacts\Model\FilterGroup as ContactFilterGroup;
use HubSpot\Client\Crm\Contacts\Model\PublicAssociationsForObject;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest as ContactSearch;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObject as ContactObject;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInputForCreate as ContactCreate;
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

    public function registerCompany(Customer $customer): CustomerRegistration
    {
        $this->getLogger()->info('Registering customer to Hubspot', ['customer_id' => (string) $customer->getId()]);
        $companyInput = new CompanyCreate();
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
            $this->getLogger()->error('Failed to register customer to Hubspot', ['customer_id' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }

        $contactReference = $this->createContact($customer, $company->getId());

        return new CustomerRegistration($company->getId(), $contactReference->reference);
    }

    public function registerContact(Customer $customer): ContactRegistration
    {
        $this->getLogger()->info('Registering contact to Hubspot', ['customer_id' => (string) $customer->getId()]);

        return $this->createContact($customer, $customer->getCrmReference());
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

        try {
            $this->client->crm()->companies()->basicApi()->update($customer->getCrmReference(), $companyInput);
        } catch (ApiException $e) {
            $this->getLogger()->error('Failed to update customer to Hubspot', ['customer_id' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }
    }

    public function search(Customer $customer): ?CustomerProfile
    {
        $this->getLogger()->info('Searching Hubspot for customer', ['customer_id' => (string) $customer->getId()]);
        $company = $this->findCompanyByName($customer->getBillingAddress()->getCompanyName());
        $contact = $this->findContact($customer);
        if (null === $company && null !== $contact) {
            $company = $this->findCompanyByContact($contact);
        }
        if (null === $company) {
            return null;
        }

        $properties = $company->getProperties();

        return new CustomerProfile(
            $company->getId(),
            $contact?->getId(),
            $properties['name'],
            $properties['city'],
            $properties['state'],
            $properties['zip'],
            $properties['country'],
        );
    }

    /**
     * @throws ApiException
     * @throws \HubSpot\Client\Crm\Associations\V4\ApiException
     * @throws ContactApiException
     */
    public function createContact(Customer $customer, string $id): ContactRegistration
    {
        $publicId = new PublicObjectId();
        $publicId->setId($id);
        $association = new PublicAssociationsForObject();
        $association->setTo($publicId);
        $associationSpec = new AssociationSpec();
        $associationSpec->setAssociationCategory('HUBSPOT_DEFINED');
        $associationSpec->setAssociationTypeId(279);  // Default HubSpot type for Company ↔ Contact
        $association->setTypes([$associationSpec]);

        $contactInput = new ContactCreate();
        $contactInput->setProperties(
            [
                'email' => $customer->getBillingEmail(),
                'company' => null,
            ]
        );
        $contactInput->setAssociations([$association]);

        try {
            $contact = $this->client->crm()->contacts()->basicApi()->create($contactInput);
        } catch (ContactApiException $e) {
            $this->getLogger()->error('Failed to register contact to Hubspot', ['customer_id' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }

        /*
        $associationSpec = ;
        try {
            $this->client->crm()->associations()->v4()->basicApi()->create(
                "companies",
                $id,
                "contacts",
                $contact->getId(),
                [$associationSpec],
            );
        } catch (\HubSpot\Client\Crm\Associations\V4\ApiException $e) {
            $this->getLogger()->error('Failed to register contact to Hubspot', ['contact_id' => $contact->getId(), 'company_id' => $id, 'customer_id' => (string)$customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }*/

        return new ContactRegistration((string) $contact->getId());
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

    private function findContact(Customer $customer): ?ContactObject
    {
        $email = $customer->getBillingEmail();
        $hubspot = $this->client;
        $filter = new ContactFilter();
        $filter
            ->setPropertyName('email')
            ->setOperator('EQ')
            ->setValue($email);

        $filterGroup = new ContactFilterGroup();
        $filterGroup->setFilters([$filter]);

        $searchRequest = new ContactSearch();
        $searchRequest->setFilterGroups([$filterGroup]);
        $searchRequest->setProperties(['name', 'address', 'city', 'state', 'zip', 'country', 'hs_object_id']);

        $contactsResponse = $hubspot->crm()->contacts()->searchApi()->doSearch($searchRequest);
        $contacts = $contactsResponse->getResults();

        if (empty($contacts)) {
            return null;
        }

        return $contacts[0];
    }

    private function findCompanyByContact(ContactObject $customer): ?SimplePublicObjectWithAssociations
    {
        $contactId = $customer->getId();
        $hubspot = $this->client;
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
