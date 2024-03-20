<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Payment;

use App\Entity\InvoiceLine;
use App\Enum\TaxType;
use App\Invoice\PricerInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class ReceiptGenerator implements ReceiptGeneratorInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PricerInterface $pricer,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    // TODO move out of this class.
    public function generateInvoiceForPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): ReceiptInterface
    {
        $payments = $this->paymentRepository->getPaymentsForCustomerDuring($startDate, $endDate, $customer);

        if (empty($payments)) {
            throw new \Exception('No payments for receipt');
        }

        $total = null;
        $vatTotal = null;
        $subTotalTotal = null;
        $subscriptions = [];
        $lines = [];

        $receipt = $this->entityFactory->getReceipt();
        foreach ($payments as $payment) {
            $subscriptions = array_merge($subscriptions, $payment->getSubscriptions()->toArray());
            $money = $payment->getMoneyAmount();

            $total = $this->addToTotal($total, $money);

            if (0 === $payment->getSubscriptions()->count()) {
                $line = $this->entityFactory->getReceiptLine();
                $line->setTotal($payment->getAmount());
                $line->setCurrency($payment->getCurrency());
                $line->setDescription($payment->getDescription());
                $line->setReceipt($receipt);

                // TODO find default? Or create Invoice for every payment.
                $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($payment->getMoneyAmount(), $customer, true, TaxType::DIGITAL_GOODS);
                $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
                $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());

                $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                $lines[] = $line;
            }
        }

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $line = new ReceiptLine();
            $line->setTotal($subscription->getAmount());
            $line->setCurrency($subscription->getCurrency());
            $line->setDescription($subscription->getPlanName());
            $line->setReceipt($receipt);

            $priceInfo = $this->pricer->getCustomerPriceInfo($subscription->getPrice(), $subscription->getCustomer(), $subscription->getSubscriptionPlan()->getProduct()->getTaxType());
            $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());

            $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
            $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

            $lines[] = $line;
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }
        if (!$line instanceof ReceiptLine) {
            throw new \LogicException('There must be at least one line');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments($payments);
        $receipt->setSubscriptions($subscriptions);
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);
        $receipt->setValid(true);
        $receipt->setCurrency($line->getCurrency());
        $receipt->setCreatedAt(new \DateTime());
        $receipt->setPayeeAddress($customer->getBillingAddress());

        return $receipt;
    }

    /**
     * @param \App\Entity\Payment $payment
     */
    public function generateReceiptForPayment(Payment $payment): ReceiptInterface
    {
        $receipt = $this->entityFactory->getReceipt();
        $total = $payment->getMoneyAmount();
        $vatTotal = null;
        $subTotalTotal = null;
        $lines = [];
        $customer = $payment->getCustomer();

        if ($payment->getInvoice()) {
            /** @var InvoiceLine $invoiceLine */
            foreach ($payment->getInvoice()->getLines() as $invoiceLine) {
                $line = $this->entityFactory->getReceiptLine();
                $line->setTotal($invoiceLine->getTotal());
                $line->setCurrency($invoiceLine->getCurrency());
                $line->setDescription($invoiceLine->getDescription());
                $line->setReceipt($receipt);
                $line->setVatPercentage($invoiceLine->getTaxPercentage());
                $line->setSubTotal($invoiceLine->getSubTotal());
                $line->setVatTotal($invoiceLine->getTaxTotal());

                $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                $lines[] = $line;
            }
        } else {
            /** @var Subscription $subscription */
            foreach ($payment->getSubscriptions() as $subscription) {
                $line = $this->entityFactory->getReceiptLine();
                $line->setTotal($subscription->getAmount());
                $line->setCurrency($subscription->getCurrency());
                $line->setDescription($subscription->getPlanName());
                $line->setReceipt($receipt);

                $priceInfo = $this->pricer->getCustomerPriceInfo($subscription->getPrice(), $subscription->getCustomer(), $subscription->getSubscriptionPlan()->getProduct()->getTaxType());
                $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
                $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
                $line->setVatPercentage($priceInfo->taxInfo->rate);

                $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                $lines[] = $line;
            }
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }

        if (!isset($line)) {
            throw new \LogicException('There must be at least one line');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments([$payment]);
        $receipt->setSubscriptions($payment->getSubscriptions());
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);
        $receipt->setValid(true);
        $receipt->setCurrency($line->getCurrency());
        $receipt->setCreatedAt(new \DateTime());
        $receipt->setVatPercentage($line->getVatPercentage());
        $receipt->setPayeeAddress($customer->getBillingAddress());

        return $receipt;
    }

    private function addToTotal(?Money $total, Money $money): Money
    {
        if (null === $total) {
            $total = $money;
        } else {
            $total = $total->plus($money, RoundingMode::HALF_EVEN);
        }

        return $total;
    }
}
