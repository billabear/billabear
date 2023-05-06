<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Factory\CustomerFactory;
use App\Factory\PaymentMethodsFactory;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Customer;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\PaymentMethodRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class CustomerImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private CustomerFactory $factory,
        private CustomerRepositoryInterface $customerRepository,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentMethodsFactory $paymentMethodsFactory,
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

                $cards = $provider->customers()->getCards($customerModel->getId(), 100);

                foreach ($cards as $paymentMethodModel) {
                    try {
                        $paymentMethod = $this->paymentMethodRepository->getPaymentMethodForReference($paymentMethodModel->getId());
                    } catch (NoEntityFoundException $exception) {
                        $paymentMethod = null;
                    }
                    $paymentMethod = $this->paymentMethodsFactory->createFromObol($paymentMethodModel, $paymentMethod);
                    $paymentMethod->setDefaultPaymentOption($customerModel->getDefaultSource() === $paymentMethod->getStoredPaymentReference());
                    $this->paymentMethodRepository->save($paymentMethod);
                    $lastId = $paymentMethodModel->getId();
                }
                $lastId = $customerModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($customerList) == $limit);
    }
}
