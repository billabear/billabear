<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice;

use App\Credit\CreditAdjustmentRecorder;
use App\Entity\Credit;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Event\InvoiceCreated;
use App\Invoice\Number\InvoiceNumberGeneratorProvider;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\VoucherApplicationRepositoryInterface;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceGenerator
{
    public function __construct(
        private PricerInterface $pricer,
        private InvoiceNumberGeneratorProvider $invoiceNumberGeneratorProvider,
        private InvoiceRepositoryInterface $invoiceRepository,
        private CreditAdjustmentRecorder $creditAdjustmentRecorder,
        private VoucherApplicationRepositoryInterface $voucherApplicationRepository,
        private EventDispatcherInterface $eventDispatcher,
        private DueDateDecider $dateDecider,
    ) {
    }

    public function generateForCustomerAndUpgrade(
        Customer $customer,
        SubscriptionPlan $oldPlan,
        SubscriptionPlan $newPlan,
        Price $oldPrice,
        Price $newPrice,
    ): Invoice {
        $lines = [];
        $total = null;
        $subTotal = null;
        $vat = null;
        $invoice = new Invoice();
        $invoice->setValid(true);
        $invoice->setInvoiceNumber($this->invoiceNumberGeneratorProvider->getGenerator()->generate());

        $diff = $oldPrice->getAsMoney()->minus($newPrice->getAsMoney())->abs();
        $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($diff, $customer, $newPrice->isIncludingTax(), $newPlan->getProduct()->getTaxType());

        $total = $total?->plus($priceInfo->total) ?? $priceInfo->total;
        $subTotal = $subTotal?->plus($priceInfo->subTotal) ?? $priceInfo->subTotal;
        $vat = $vat?->plus($priceInfo->vat) ?? $priceInfo->vat;

        $line = new InvoiceLine();
        $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
        $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
        $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
        $line->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
        $line->setInvoice($invoice);
        $line->setDescription(sprintf('Change from %s at %s to %s at %s', $oldPlan->getName(), $oldPrice->getAsMoney(), $newPlan->getName(), $newPrice->getAsMoney()));
        $line->setTaxPercentage($priceInfo->taxInfo->rate);
        $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);
        $line->setTaxCountry($priceInfo->taxInfo->country);
        $line->setTaxType($newPlan->getProduct()->getTaxType());
        $lines[] = $line;

        return $this->finaliseInvoice($customer, $invoice, $total, $lines, $subTotal, $priceInfo, $vat);
    }

    /**
     * @param Subscription[] $subscriptions
     */
    public function generateForCustomerAndSubscriptions(Customer $customer, array $subscriptions, array $inputLines = []): Invoice
    {
        if (empty($subscriptions) && empty($inputLines)) {
            throw new \Exception("Can't generate invoices for no subscription");
        }

        $lines = [];
        $total = null;
        $subTotal = null;
        $vat = null;
        $invoice = new Invoice();
        $invoice->setValid(true);
        $invoice->setInvoiceNumber($this->invoiceNumberGeneratorProvider->getGenerator()->generate());

        foreach ($subscriptions as $subscription) {
            $price = $subscription->getPrice();

            if (!$price instanceof Price) {
                throw new \Exception(sprintf("The subscription '%s' has no price", $subscription->getPlanName()));
            }

            $taxType = $subscription->getSubscriptionPlan()->getProduct()->getTaxType();
            $priceInfo = $this->pricer->getCustomerPriceInfo($price, $customer, $taxType, $subscription->getSeats());

            $total = $total?->plus($priceInfo->total) ?? $priceInfo->total;
            $subTotal = $subTotal?->plus($priceInfo->subTotal) ?? $priceInfo->subTotal;
            $vat = $vat?->plus($priceInfo->vat) ?? $priceInfo->vat;

            $line = new InvoiceLine();
            $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
            $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $line->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setInvoice($invoice);
            if (null !== $subscription->getSeats() && $subscription->getSeats() > 1) {
                $line->setDescription(sprintf('%d x %s', $subscription->getSeats(), $subscription->getPlanName()));
            } else {
                $line->setDescription($subscription->getPlanName());
            }
            $line->setTaxPercentage($priceInfo->taxInfo->rate);
            $line->setTaxType($taxType);
            $line->setTaxCountry($priceInfo->taxInfo->country);
            $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);
            $lines[] = $line;
        }
        $invoice->setSubscriptions($subscriptions);

        /** @var LineItem $lineItem */
        foreach ($inputLines as $lineItem) {
            $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($lineItem->getMoney(), $customer, $lineItem->isIncludeTax(), $lineItem->getTaxType());

            $total = $total?->plus($priceInfo->total) ?? $priceInfo->total;
            $subTotal = $subTotal?->plus($priceInfo->subTotal) ?? $priceInfo->subTotal;
            $vat = $vat?->plus($priceInfo->vat) ?? $priceInfo->vat;

            $line = new InvoiceLine();
            $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
            $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $line->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setInvoice($invoice);
            $line->setDescription($lineItem->getDescription());
            $line->setTaxPercentage($priceInfo->taxInfo->rate);
            $line->setTaxType($lineItem->getTaxType());
            $line->setTaxCountry($priceInfo->taxInfo->country);
            $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);
            $lines[] = $line;
        }

        return $this->finaliseInvoice($customer, $invoice, $total, $lines, $subTotal, $priceInfo, $vat);
    }

    /**
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    protected function finaliseInvoice(Customer $customer, Invoice $invoice, ?\Brick\Money\Money $total, array $lines, ?\Brick\Money\Money $subTotal, PriceInfo $priceInfo, ?\Brick\Money\Money $vat): Invoice
    {
        if ($customer->hasCredit() && !$customer->getCreditAsMoney()->isZero()) {
            $line = new InvoiceLine();
            $line->setCurrency($customer->getCreditCurrency());
            $line->setInvoice($invoice);
            $line->setTaxTotal(0);
            $line->setTaxPercentage(0);
            if ($customer->getCreditAsMoney()->isPositive()) {
                $amount = $customer->getCreditAsMoney()->negated();
                if ($total->plus($amount)->isPositive()) {
                    $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_DEBIT, $customer, $amount->abs());

                    $customer->setCreditAmount(null);
                    $customer->setCreditCurrency(null);
                } else {
                    $minus = $customer->getCreditAsMoney()->minus($total);
                    $amount = $amount->plus($minus);

                    $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_DEBIT, $customer, $amount->abs());
                    $customer->addCreditAsMoney($amount);
                }
            } else {
                $amount = $customer->getCreditAsMoney()->abs();
                $customer->setCreditAmount(null);
                $customer->setCreditCurrency(null);
                $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_CREDIT, $customer, $amount);
            }

            $line->setTotal($amount->getMinorAmount()->toInt());
            $line->setSubTotal($amount->getMinorAmount()->toInt());
            $line->setDescription('Credit adjustment');
            $lines[] = $line;
            $total = $total?->plus($amount) ?? $amount;
            $subTotal = $subTotal?->plus($amount) ?? $amount;
        }

        try {
            $voucherApplication = $this->voucherApplicationRepository->findUnUsedForCustomer($customer);

            $line = new InvoiceLine();
            $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
            $line->setInvoice($invoice);

            $percentage = $voucherApplication->getVoucher()->getPercentage();
            $percentage /= 100;
            $amount = $total->multipliedBy($percentage)->negated();

            $vatAmount = $vat->multipliedBy($percentage)->negated();

            $line->setDescription($voucherApplication->getVoucher()->getName());
            $line->setTaxPercentage($vatAmount->getMinorAmount()->toInt());
            $line->setSubTotal($amount->getMinorAmount()->toInt());
            $line->setTotal($amount->getMinorAmount()->toInt());
            $line->setTaxTotal(0);

            $vat = $vat?->plus($vatAmount);
            $total = $total->plus($amount);
            $subTotal = $subTotal->plus($amount);

            $lines[] = $line;
            $voucherApplication->setUsed(true);
            $this->voucherApplicationRepository->save($voucherApplication);
        } catch (NoEntityFoundException $e) {
        }

        $invoice->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
        $invoice->setLines($lines);
        $invoice->setTaxTotal($vat->getMinorAmount()->toInt());
        $invoice->setTotal($total->getMinorAmount()->toInt());
        $invoice->setAmountDue($total->getMinorAmount()->toInt());
        $invoice->setSubTotal($subTotal->getMinorAmount()->toInt());
        $invoice->setPaid(false);
        $invoice->setCreatedAt(new \DateTime('now'));
        $invoice->setUpdatedAt(new \DateTime('now'));
        $invoice->setCustomer($customer);
        $invoice->setPayeeAddress($customer->getBillingAddress());
        $invoice->setBillerAddress($customer->getBrandSettings()->getAddress());

        $this->dateDecider->setDueAt($invoice);

        $this->invoiceRepository->save($invoice);

        $this->eventDispatcher->dispatch(new InvoiceCreated($invoice), InvoiceCreated::NAME);

        return $invoice;
    }
}
