<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\Payment;
use BillaBear\Event\Invoice\InvoicePaid;
use BillaBear\Repository\InvoiceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Obol\Exception\PaymentFailureException;
use Obol\Exception\ProviderFailureException;
use Obol\Model\Charge;
use Obol\Model\Enum\ChargeFailureReasons;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Event\PaymentCreated;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Lazy]
class InvoiceCharger
{
    use LoggerAwareTrait;

    public function __construct(
        private ProviderInterface $provider,
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private BillingDetailsFactoryInterface $billingDetailsFactory,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private EventDispatcherInterface $eventDispatcher,
        private InvoiceRepositoryInterface $invoiceRepository,
        private PaymentFailureHandler $paymentFailureHandler,
    ) {
    }

    public function chargeInvoice(Invoice $invoice, ?PaymentCard $paymentCard = null, ?\DateTime $createdAt = null): bool
    {
        if (!$paymentCard) {
            try {
                $paymentCard = $this->paymentCardRepository->getDefaultPaymentCardForCustomer($invoice->getCustomer());
            } catch (NoEntityFoundException) {
                $this->getLogger()->warning('Failed to get default payment card for customer', ['customer_id' => $invoice->getCustomer()->getId()]);

                return false;
            }
        }
        $billingDetails = $this->billingDetailsFactory->createFromCustomerAndPaymentDetails($invoice->getCustomer(), $paymentCard);

        $charge = new Charge();
        $charge->setName('BillaBear');
        $charge->setAmount($invoice->getTotalMoney());
        $charge->setBillingDetails($billingDetails);

        try {
            $response = $this->provider->payments()->chargeCardOnFile($charge);
        } catch (PaymentFailureException $exception) {
            $this->getLogger()->warning('Failed to charge invoice with payment failure', ['reason' => $exception->getReason()->value, 'invoice_id' => $invoice->getId()]);
            $this->paymentFailureHandler->handleInvoiceAndResponse($invoice, $exception->getReason());

            throw $exception;
        } catch (ProviderFailureException $exception) {
            $this->getLogger()->warning('Failed to charge invoice with general payment provider failure', [
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
                'invoice_id' => $invoice->getId(),
            ]);

            $this->paymentFailureHandler->handleInvoiceAndResponse($invoice, $exception->getMessage());

            throw new PaymentFailureException(ChargeFailureReasons::GENERAL_DECLINE, $exception);
        }

        /** @var Payment $payment */
        $payment = $this->paymentFactory->fromSubscriptionCreation($response->getPaymentDetails(), $invoice->getCustomer());

        if ($createdAt) {
            $payment->setCreatedAt($createdAt);
            $invoice->setPaidAt($createdAt);
        } else {
            $invoice->setPaidAt(new \DateTime('now'));
        }

        foreach ($invoice->getSubscriptions() as $subscription) {
            $payment->addSubscription($subscription);
        }
        $payment->setInvoice($invoice);

        $this->paymentRepository->save($payment);
        $invoice->setPayments(new ArrayCollection([$payment]));
        $invoice->setPaid(true);
        $this->invoiceRepository->save($invoice);

        $this->getLogger()->info('Invoice has successfully been charged', ['invoice_id' => $invoice->getId()]);

        $this->eventDispatcher->dispatch(new InvoicePaid($invoice), InvoicePaid::NAME);
        $this->eventDispatcher->dispatch(new PaymentCreated($payment, true), PaymentCreated::NAME);

        return true;
    }
}
