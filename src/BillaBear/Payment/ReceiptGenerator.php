<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\Customer;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\ReceiptLine;
use BillaBear\Pricing\PricerInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Common\LoggerAwareTrait;

class ReceiptGenerator implements ReceiptGeneratorInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private PricerInterface $pricer,
        private EntityFactoryInterface $entityFactory,
        private TaxTypeRepositoryInterface $taxTypeRepository,
    ) {
    }

    /**
     * @param \BillaBear\Entity\Payment $payment
     */
    public function generateReceiptForPayment(Payment $payment): ReceiptInterface
    {
        $receipt = $this->entityFactory->getReceipt();
        $total = $payment->getMoneyAmount();
        $vatTotal = null;
        $subTotalTotal = null;
        $lines = [];
        /** @var Customer $customer */
        $customer = $payment->getCustomer();

        if ($payment->getInvoice()) {
            $this->getLogger()->debug('Create receipt from invoice', ['payment_id' => (string) $payment->getId()]);
            /** @var InvoiceLine $invoiceLine */
            foreach ($payment->getInvoice()->getLines() as $invoiceLine) {
                /** @var ReceiptLine $line */
                $line = $this->entityFactory->getReceiptLine();
                $line->setTotal($invoiceLine->getTotal());
                $line->setCurrency($invoiceLine->getCurrency());
                $line->setDescription($invoiceLine->getDescription());
                $line->setReceipt($receipt);
                $line->setVatPercentage($invoiceLine->getTaxPercentage());
                $line->setSubTotal($invoiceLine->getSubTotal());
                $line->setVatTotal($invoiceLine->getTaxTotal());
                $line->setTaxType($invoiceLine->getTaxType());
                $line->setReverseCharge($invoiceLine->isReverseCharge());
                $line->setTaxCountry($invoiceLine->getTaxCountry());

                $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                $lines[] = $line;
            }
        } else {
            $this->getLogger()->debug('Creating receipt from subscriptions', ['payment_id' => (string) $payment->getId()]);
            /** @var Subscription $subscription */
            foreach ($payment->getSubscriptions() as $subscription) {
                $taxType = $subscription->getSubscriptionPlan()->getProduct()->getTaxType();

                $priceInfos = $this->pricer->getCustomerPriceInfo($subscription->getPrice(), $subscription->getCustomer(), $taxType);
                foreach ($priceInfos as $priceInfo) {
                    /** @var ReceiptLine $line */
                    $line = $this->entityFactory->getReceiptLine();
                    $line->setTotal($subscription->getAmount());
                    $line->setCurrency($subscription->getCurrency());
                    $line->setDescription($subscription->getPlanName());
                    $line->setReceipt($receipt);
                    $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
                    $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
                    $line->setVatPercentage($priceInfo->taxInfo->rate);
                    $line->setTaxType($taxType);
                    $line->setTaxCountry($priceInfo->taxInfo->country);
                    $line->setTaxState($priceInfo->taxInfo->state);
                    $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);

                    $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                    $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                    $lines[] = $line;
                }
            }
        }

        if (!$total instanceof Money) {
            $this->getLogger()->error("Attempted to create a receipt for a payment that didn't have an amount", ['payment_id' => (string) $payment->getId()]);
            throw new \LogicException('Total must be money if payments exist');
        }

        if (!isset($line)) {
            $this->getLogger()->debug('Creating a receipt from just the payment.', ['payment_id' => (string) $payment->getId()]);
            $taxType = $this->taxTypeRepository->getDefault();
            $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($total, $payment->getCustomer(), true, $taxType);
            $line = $this->entityFactory->getReceiptLine();
            $line->setTotal($payment->getAmount());
            $line->setCurrency($payment->getCurrency());
            $line->setDescription('Standalone payment');
            $line->setReceipt($receipt);

            $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $line->setVatPercentage($priceInfo->taxInfo->rate);
            $line->setTaxType($taxType);
            $line->setTaxCountry($priceInfo->taxInfo->country);
            $line->setTaxState($priceInfo->taxInfo->state);
            $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);

            $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
            $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

            $lines[] = $line;
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
        $receipt->setBillerAddress($customer->getBrandSettings()->getAddress());

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

    public function generateInvoiceForPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): ReceiptInterface
    {
        throw new \Exception('TODO fix ISP breakage');
    }
}
