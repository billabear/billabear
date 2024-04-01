<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\StripeImportRepositoryInterface;
use App\Stats\PaymentAmountStats;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentLinkerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class PaymentImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private CustomerRepositoryInterface $customerRepository,
        private PaymentFactoryInterface $factory,
        private PaymentLinkerInterface $paymentLinker,
        private PaymentAmountStats $paymentAmountStats,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $paymentList = $provider->payments()->list($limit, $lastId);
            /** @var PaymentDetails $paymentDetails */
            foreach ($paymentList as $paymentDetails) {
                try {
                    $payment = $this->paymentRepository->getPaymentForReference($paymentDetails->getPaymentReference());
                } catch (NoEntityFoundException $e) {
                    $customer = null;
                    if ($paymentDetails->getCustomerReference()) {
                        $customer = $this->customerRepository->getByExternalReference($paymentDetails->getCustomerReference());
                    }

                    $payment = $this->factory->createFromPaymentDetails($paymentDetails, $customer);
                    $payment->setCreatedAt($paymentDetails->getCreatedAt());
                    $this->paymentLinker->linkPaymentDetailsToSubscription($payment, $paymentDetails);
                    $this->paymentRepository->save($payment);

                    $this->paymentAmountStats->process($payment);
                }
                $lastId = $paymentDetails->getPaymentReference();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($paymentList) == $limit);
        $stripeImport->setLastId(null);
    }
}
