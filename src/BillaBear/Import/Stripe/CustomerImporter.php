<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\Entity\Customer as CustomerEntity;
use BillaBear\Entity\StripeImport;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Repository\StripeImportRepositoryInterface;
use Obol\Model\Customer;
use Obol\Provider\ProviderInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class CustomerImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CustomerDataMapper $factory,
        private CustomerRepositoryInterface $customerRepository,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private PaymentMethodsDataMapper $paymentMethodsFactory,
        private CustomerCreditImporter $customerCreditImporter,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        $brand = $this->brandSettingsRepository->getByCode(CustomerEntity::DEFAULT_BRAND);
        do {
            $customerList = $provider->customers()->list($limit, $lastId);
            /** @var Customer $customerModel */
            foreach ($customerList as $customerModel) {
                try {
                    $customer = $this->customerRepository->getByExternalReference($customerModel->getId());
                } catch (NoEntityFoundException $exception) {
                    $customer = null;
                }
                $customer = $this->factory->createCustomerFromObol($customerModel, $customer);
                $customer->setBrand(CustomerEntity::DEFAULT_BRAND);
                $customer->setBrandSettings($brand);

                $this->customerRepository->save($customer);
                $this->customerCreditImporter->importForCustomer($customer);

                $cards = $provider->customers()->getCards($customerModel->getId(), 100);

                foreach ($cards as $paymentMethodModel) {
                    try {
                        $paymentMethod = $this->paymentCardRepository->getPaymentCardForReference($paymentMethodModel->getId());
                    } catch (NoEntityFoundException $exception) {
                        $paymentMethod = null;
                    }
                    $paymentMethod = $this->paymentMethodsFactory->createFromObol($paymentMethodModel, $paymentMethod);
                    $paymentMethod->setDefaultPaymentOption($customerModel->getDefaultSource() === $paymentMethod->getStoredPaymentReference());
                    $this->paymentCardRepository->save($paymentMethod);
                }
                $lastId = $customerModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($customerList) == $limit);
        $stripeImport->setLastId(null);
    }
}
