<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
