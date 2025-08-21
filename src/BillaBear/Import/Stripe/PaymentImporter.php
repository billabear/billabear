<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\Entity\StripeImport;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\StripeImportRepositoryInterface;
use BillaBear\Stats\PaymentAmountStats;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentLinkerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
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
        private ReceiptGeneratorInterface $receiptGenerator,
        private ReceiptRepositoryInterface $receiptRepository,
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
                    $receipt = $this->receiptGenerator->generateReceiptForPayment($payment);
                    $this->receiptRepository->save($receipt);

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
