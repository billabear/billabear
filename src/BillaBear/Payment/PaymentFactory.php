<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\Customer;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use Obol\Model\Events\AbstractCharge;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

#[Lazy]
class PaymentFactory implements PaymentFactoryInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerProviderInterface $customerProvider,
        private ProviderInterface $provider,
        private EntityFactoryInterface $entityFactory,
        private PaymentCardRepositoryInterface $paymentCardRepository,
    ) {
    }

    /**
     * @param Customer $customer
     */
    public function createFromPaymentDetails(PaymentDetails $paymentDetails, ?CustomerInterface $customer = null): Payment
    {
        /** @var \BillaBear\Entity\Payment $payment */
        $payment = $this->entityFactory->getPaymentEntity();
        $payment->setPaymentReference($paymentDetails->getPaymentReference());
        $payment->setPaymentProviderDetailsUrl($paymentDetails->getPaymentReferenceLink());
        $payment->setMoneyAmount($paymentDetails->getAmount());
        if (isset($customer)) {
            $payment->setCustomer($customer);
            $payment->setCountry($customer->getCountry());
            $payment->setState($customer->getBillingAddress()->getRegion());
        }
        $payment->setCompleted(true);
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setProvider($this->provider->getName());

        try {
            $paymentCard = $this->paymentCardRepository->getByStoredPaymentReference($paymentDetails->getStoredPaymentReference());
            $payment->setPaymentCard($paymentCard);
        } catch (NoEntityFoundException $e) {
            $this->logger->critical('Unable to find payment card for payment', ['stored_payment_reference' => $paymentDetails->getStoredPaymentReference()]);

            throw $e;
        }

        return $payment;
    }

    public function fromSubscriptionCreation(PaymentDetails $paymentDetails, ?CustomerInterface $customer = null): Payment
    {
        if (!$customer) {
            $customer = $this->customerProvider->getCurrentCustomer();
        }

        return $this->createFromPaymentDetails($paymentDetails, $customer);
    }

    public function fromChargeEvent(AbstractCharge $charge): Payment
    {
        $payment = $this->entityFactory->getPaymentEntity();
        $payment->setPaymentReference($charge->getPaymentReference());
        $payment->setPaymentProviderDetailsUrl($charge->getDetailsLink());
        $payment->setAmount($charge->getAmount());
        $payment->setCurrency($charge->getCurrency());
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setProvider($this->provider->getName());

        try {
            $paymentCard = $this->paymentCardRepository->getByStoredPaymentReference($charge->getExternalPaymentMethodId());
            $payment->setPaymentCard($paymentCard);
        } catch (NoEntityFoundException $e) {
            $this->logger->critical('Unable to find payment card for payment', ['stored_payment_reference' => $charge->getExternalPaymentMethodId()]);

            throw $e;
        }

        return $payment;
    }
}
