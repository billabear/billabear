<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\DataMappers\CustomerDataMapper;
use App\DataMappers\PaymentMethodsDataMapper;
use App\Entity\StripeImport;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\PaymentCardRepositoryInterface;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Customer;
use Obol\Provider\ProviderInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

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
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
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
