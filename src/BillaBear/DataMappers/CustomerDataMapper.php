<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\CustomerType;
use BillaBear\Dto\Generic\Address as AddressDto;
use BillaBear\Dto\Generic\Api\Customer as CustomerApiDto;
use BillaBear\Dto\Generic\App\Customer as CustomerAppDto;
use BillaBear\Dto\Generic\Public\Customer as CustomerPublicDto;
use BillaBear\Dto\Request\Api\CreateCustomerDto as ApiCreate;
use BillaBear\Dto\Request\App\CreateCustomerDto as AppCreate;
use BillaBear\Dto\Request\Public\CreateCustomerDto as PublicCreate;
use BillaBear\Entity\Customer;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use Obol\Model\Customer as ObolCustomer;
use Parthenon\Common\Address;

class CustomerDataMapper
{
    public function __construct(private BrandSettingsRepositoryInterface $brandSettingRepository)
    {
    }

    public function createCustomerFromObol(ObolCustomer $obolCustomer, ?Customer $customer = null): Customer
    {
        if (!$customer) {
            $customer = new Customer();
            $customer->setStatus(CustomerStatus::NEW);
        }

        $customer->setBillingEmail($obolCustomer->getEmail());
        $customer->setName($obolCustomer->getName());
        $customer->setLocale(Customer::DEFAULT_LOCALE);
        $customer->setExternalCustomerReference($obolCustomer->getId());
        $customer->setPaymentProviderDetailsUrl($obolCustomer->getUrl());
        $customer->setReference($obolCustomer->getDescription());
        $customer->setCreatedAt($obolCustomer->getCreatedAt());
        $customer->setType(CustomerType::BUSINESS);

        $address = new Address();
        $address->setStreetLineTwo($obolCustomer->getAddress()->getStreetLineOne());
        $address->setStreetLineTwo($obolCustomer->getAddress()->getStreetLineTwo());
        $address->setCity($obolCustomer->getAddress()->getCity());
        $address->setRegion($obolCustomer->getAddress()->getState());
        $address->setPostcode($obolCustomer->getAddress()->getPostalCode());
        $address->setCountry($obolCustomer->getAddress()->getCountryCode());

        $customer->setBillingAddress($address);

        return $customer;
    }

    public function createCustomer(ApiCreate|AppCreate|PublicCreate $createCustomerDto, ?Customer $customer = null): Customer
    {
        $address = new Address();
        $address->setCompanyName($createCustomerDto->getAddress()?->getCompanyName());
        $address->setStreetLineOne($createCustomerDto->getAddress()?->getStreetLineOne());
        $address->setStreetLineTwo($createCustomerDto->getAddress()?->getStreetLineTwo());
        $address->setCountry($createCustomerDto->getAddress()?->getCountry());
        $address->setCity($createCustomerDto->getAddress()?->getCity());
        $address->setRegion($createCustomerDto->getAddress()?->getRegion());
        $address->setPostcode($createCustomerDto->getAddress()?->getPostcode());

        if (!$customer) {
            $customer = new Customer();
            $customer->setStatus(CustomerStatus::NEW);
            $customer->setCreatedAt(new \DateTime('now'));
        }

        $type = match (strtolower($createCustomerDto->getType() ?? '')) {
            'business' => CustomerType::BUSINESS,
            default => CustomerType::INDIVIDUAL,
        };

        $customer->setType($type);
        $customer->setBillingEmail($createCustomerDto->getEmail());
        $customer->setReference($createCustomerDto->getReference());
        $customer->setBillingAddress($address);
        $customer->setName($createCustomerDto->getName());
        $customer->setBrand($createCustomerDto->getBrand() ?? Customer::DEFAULT_BRAND);
        $customer->setLocale($createCustomerDto->getLocale() ?? Customer::DEFAULT_LOCALE);
        $customer->setBillingType($createCustomerDto->getBillingType() ?? Customer::DEFAULT_BILLING_TYPE);
        $customer->setTaxNumber($createCustomerDto->getTaxNumber());
        $customer->setStandardTaxRate($createCustomerDto->getStandardTaxrate());
        $customer->setInvoiceFormat($createCustomerDto->getInvoiceFormat());
        $customer->setMarketingOptIn($createCustomerDto->getMarketingOptIn());
        $customer->setMetadata($createCustomerDto->getMetadata());

        $brandSettings = $this->brandSettingRepository->getByCode($customer->getBrand());
        $customer->setBrandSettings($brandSettings);

        $externalCustomerReference = $createCustomerDto->getExternalReference();

        if (isset($externalCustomerReference)) {
            $customer->setExternalCustomerReference($externalCustomerReference);
            $customer->setPaymentProviderDetailsUrl(null);
        }

        return $customer;
    }

    public function createApiDto(Customer $customer): CustomerApiDto
    {
        $address = new AddressDto();
        $address->setCompanyName($customer->getBillingAddress()->getCompanyName());
        $address->setStreetLineOne($customer->getBillingAddress()->getStreetLineOne());
        $address->setStreetLineTwo($customer->getBillingAddress()->getStreetLineTwo());
        $address->setCity($customer->getBillingAddress()->getCity());
        $address->setRegion($customer->getBillingAddress()->getRegion());
        $address->setCountry($customer->getBillingAddress()->getCountry());
        $address->setPostcode($customer->getBillingAddress()->getPostcode());

        $dto = new CustomerApiDto();
        $dto->setName($customer->getName());
        $dto->setId((string) $customer->getId());
        $dto->setReference($customer->getReference());
        $dto->setEmail($customer->getBillingEmail());
        $dto->setExternalReference($customer->getExternalCustomerReference());
        $dto->setAddress($address);
        $dto->setStatus($customer->getStatus()->value);
        $dto->setBrand($customer->getBrand());
        $dto->setLocale($customer->getLocale());
        $dto->setBillingType($customer->getBillingType());
        $dto->setTaxNumber($customer->getTaxNumber());
        $dto->setStandardTaxRate($customer->getStandardTaxRate());
        $dto->setType($customer->getType()->value);
        $dto->setInvoiceFormat($customer->getInvoiceFormat());
        $dto->setMarketingOptIn($customer->getMarketingOptIn());

        return $dto;
    }

    public function createAppDto(?Customer $customer): ?CustomerAppDto
    {
        if (!$customer) {
            return null;
        }

        $address = new AddressDto();
        $address->setCompanyName($customer->getBillingAddress()->getCompanyName());
        $address->setStreetLineOne($customer->getBillingAddress()->getStreetLineOne());
        $address->setStreetLineTwo($customer->getBillingAddress()->getStreetLineTwo());
        $address->setCity($customer->getBillingAddress()->getCity());
        $address->setRegion($customer->getBillingAddress()->getRegion());
        $address->setCountry($customer->getBillingAddress()->getCountry());
        $address->setPostcode($customer->getBillingAddress()->getPostcode());

        $dto = new CustomerAppDto();
        $dto->setName($customer->getName());
        $dto->setId((string) $customer->getId());
        $dto->setReference($customer->getReference());
        $dto->setEmail($customer->getBillingEmail());
        $dto->setExternalReference($customer->getExternalCustomerReference());
        $dto->setAddress($address);
        $dto->setStatus($customer->getStatus()->value);
        $dto->setBrand($customer->getBrand());
        $dto->setLocale($customer->getLocale());
        $dto->setBillingType($customer->getBillingType());
        $dto->setTaxNumber($customer->getTaxNumber());
        $dto->setStandardTaxRate($customer->getStandardTaxRate());
        $dto->setType($customer->getType()->value);
        $dto->setInvoiceFormat($customer->getInvoiceFormat());
        $dto->setCreatedAt($customer->getCreatedAt());
        $dto->setMarketingOptIn($customer->getMarketingOptIn());

        return $dto;
    }

    public function createPublicDto(Customer $customer): CustomerPublicDto
    {
        $address = new AddressDto();
        $address->setStreetLineOne($customer->getBillingAddress()->getStreetLineOne());
        $address->setStreetLineTwo($customer->getBillingAddress()->getStreetLineTwo());
        $address->setCity($customer->getBillingAddress()->getCity());
        $address->setRegion($customer->getBillingAddress()->getRegion());
        $address->setCountry($customer->getBillingAddress()->getCountry());
        $address->setPostcode($customer->getBillingAddress()->getPostcode());

        $dto = new CustomerPublicDto();
        $dto->setName($customer->getName());
        $dto->setId((string) $customer->getId());
        $dto->setEmail($customer->getBillingEmail());
        $dto->setAddress($address);
        $dto->setBrand($customer->getBrand());
        $dto->setLocale($customer->getLocale());
        $dto->setType($customer->getType()->value);

        return $dto;
    }
}
