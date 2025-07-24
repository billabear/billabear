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
        $address->setCompanyName($createCustomerDto->address?->companyName);
        $address->setStreetLineOne($createCustomerDto->address?->streetLineOne);
        $address->setStreetLineTwo($createCustomerDto->address?->streetLineTwo);
        $address->setCountry($createCustomerDto->address?->country);
        $address->setCity($createCustomerDto->address?->city);
        $address->setRegion($createCustomerDto->address?->region);
        $address->setPostcode($createCustomerDto->address?->postcode);

        if (!$customer) {
            $customer = new Customer();
            $customer->setStatus(CustomerStatus::NEW);
            $customer->setCreatedAt(new \DateTime('now'));
        }

        $type = match (strtolower($createCustomerDto->type ?? '')) {
            'business' => CustomerType::BUSINESS,
            default => CustomerType::INDIVIDUAL,
        };

        $customer->setType($type);
        $customer->setBillingEmail($createCustomerDto->email);
        $customer->setReference($createCustomerDto->reference);
        $customer->setBillingAddress($address);
        $customer->setName($createCustomerDto->name);
        $customer->setBrand($createCustomerDto->brand ?? Customer::DEFAULT_BRAND);
        $customer->setLocale($createCustomerDto->locale ?? Customer::DEFAULT_LOCALE);
        $customer->setBillingType($createCustomerDto->billingType ?? Customer::DEFAULT_BILLING_TYPE);
        $customer->setTaxNumber($createCustomerDto->tax_number);
        $customer->setStandardTaxRate($createCustomerDto->standard_tax_rate);
        $customer->setInvoiceFormat($createCustomerDto->invoice_format);
        $customer->setMarketingOptIn($createCustomerDto->marketing_opt_in);
        $customer->setMetadata($createCustomerDto->metadata ?? []);

        $brandSettings = $this->brandSettingRepository->getByCode($customer->getBrand());
        $customer->setBrandSettings($brandSettings);

        $externalCustomerReference = $createCustomerDto->externalReference;

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

        $dto = new CustomerApiDto(
            (string) $customer->getId(),
            $customer->getName(),
            $customer->getBillingEmail(),
            $customer->getReference(),
            $customer->getExternalCustomerReference(),
            $address,
            $customer->getStatus()->value,
            $customer->getBrand(),
            $customer->getLocale(),
            $customer->getBillingType(),
            $customer->getTaxNumber(),
            $customer->getStandardTaxRate(),
            $customer->getType()->value,
            $customer->getInvoiceFormat(),
            $customer->getMarketingOptIn(),
            $customer->getMetadata()
        );

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

        $dto = new CustomerAppDto(
            (string) $customer->getId(),
            $customer->getName(),
            $customer->getBillingEmail(),
            $customer->getReference(),
            $customer->getExternalCustomerReference(),
            null,
            $address,
            $customer->getStatus()->value,
            $customer->getBrand(),
            $customer->getLocale(),
            $customer->getBillingType(),
            $customer->getTaxNumber(),
            $customer->getStandardTaxRate(),
            $customer->getType()->value,
            $customer->getInvoiceFormat(),
            $customer->getMarketingOptIn(),
            $customer->getCreatedAt(),
            $customer->getMetadata(),
        );

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

        $dto = new CustomerPublicDto(
            (string) $customer->getId(),
            $customer->getName(),
            $customer->getBillingEmail(),
            $address,
            $customer->getBrand(),
            $customer->getLocale(),
            $customer->getType()->value,
            $customer->getBillingType(),
        );

        return $dto;
    }
}
