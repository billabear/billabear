<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Credit\CreditAdjustmentRecorder;
use BillaBear\Entity\Credit;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoicedMetricCounter;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\Price;
use BillaBear\Entity\Subscription;
use BillaBear\Event\Invoice\InvoiceCreated;
use BillaBear\Exception\Invoice\NothingToInvoiceException;
use BillaBear\Invoice\Number\InvoiceNumberGeneratorProvider;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Payment\ExchangeRates\ToSystemConverter;
use BillaBear\Pricing\PricerInterface;
use BillaBear\Pricing\Usage\MetricProvider;
use BillaBear\Pricing\Usage\MetricType;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\Usage\MetricCounterRepositoryInterface;
use BillaBear\Repository\VoucherApplicationRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceGenerator
{
    private CurrencyConverter $currencyConverter;

    public function __construct(
        private PricerInterface $pricer,
        private InvoiceNumberGeneratorProvider $invoiceNumberGeneratorProvider,
        private InvoiceRepositoryInterface $invoiceRepository,
        private CreditAdjustmentRecorder $creditAdjustmentRecorder,
        private VoucherApplicationRepositoryInterface $voucherApplicationRepository,
        private EventDispatcherInterface $eventDispatcher,
        private DueDateDecider $dateDecider,
        private MetricProvider $metricProvider,
        private MetricCounterRepositoryInterface $metricUsageRepository,
        private QuantityProvider $quantityProvider,
        private ToSystemConverter $toSystemConverter,
        BricksExchangeRateProvider $exchangeRateProvider,
    ) {
        $this->currencyConverter = new CurrencyConverter($exchangeRateProvider);
    }

    public function generateForCustomerAndUpgrade(
        Customer $customer,
        SubscriptionPlan $oldPlan,
        SubscriptionPlan $newPlan,
        Price $oldPrice,
        Price $newPrice,
        Subscription $subscription,
        ?Money $diff = null,
    ): Invoice {
        $lines = [];
        $total = null;
        $subTotal = null;
        $vat = null;
        $createdAt = new \DateTime('now');
        $invoice = new Invoice();
        $invoice->setValid(true);
        $invoice->setInvoiceNumber($this->invoiceNumberGeneratorProvider->getGenerator()->generate());
        $invoice->setSubscriptions([$subscription]);

        if (!$diff) {
            $diff = $oldPrice->getAsMoney()->minus($newPrice->getAsMoney());
        }

        $diff = $diff->abs();

        list($priceInfo, $total, $subTotal, $vat, $lines) = $this->buildForPrice(
            $subscription, $newPlan, $newPrice, $customer, $createdAt, $invoice, $total, $subTotal, $vat, $lines, $diff
        );

        $line = new InvoiceLine();
        $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
        $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
        $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
        $line->setNetPrice($priceInfo->netPrice->getMinorAmount()->toInt());
        $line->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
        $line->setConvertedTotal($this->toSystemConverter->convert($priceInfo->total)->getMinorAmount()->toInt());
        $line->setConvertedSubTotal($this->toSystemConverter->convert($priceInfo->subTotal)->getMinorAmount()->toInt());
        $line->setConvertedTaxTotal($this->toSystemConverter->convert($priceInfo->vat)->getMinorAmount()->toInt());
        $line->setConvertedNetPrice($this->toSystemConverter->convert($priceInfo->netPrice)->getMinorAmount()->toInt());
        $line->setInvoice($invoice);
        $line->setDescription(sprintf('Change from %s at %s to %s at %s', $oldPlan->getName(), $oldPrice->getAsMoney(), $newPlan->getName(), $newPrice->getAsMoney()));
        $line->setTaxPercentage($priceInfo->taxInfo->rate);
        $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);
        $line->setTaxCountry($priceInfo->taxInfo->country);
        $line->setTaxState($priceInfo->taxInfo->state);
        $line->setTaxType($newPlan->getProduct()->getTaxType());
        $lines[] = $line;

        return $this->finaliseInvoice($customer, $invoice, $total, $lines, $subTotal, $priceInfo->total->getCurrency()->getCurrencyCode(), $vat, $createdAt);
    }

    /**
     * @param Subscription[] $subscriptions
     */
    public function generateForCustomerAndSubscriptions(Customer $customer, array $subscriptions, array $inputLines = [], ?\DateTime $createdAt = null): Invoice
    {
        if (empty($subscriptions) && empty($inputLines)) {
            throw new \Exception("Can't generate invoices for no subscription");
        }

        if (!$createdAt) {
            $createdAt = new \DateTime('now');
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

            if ($price instanceof Price) {
                list($priceInfo, $total, $subTotal, $vat, $lines) = $this->buildForPrice($subscription, $subscription->getSubscriptionPlan(), $price, $customer, $createdAt, $invoice, $total, $subTotal, $vat, $lines);
            } else {
                $line = new InvoiceLine();
                $line->setInvoice($invoice);
                $line->setCurrency('USD');
                $line->setTotal(0);
                $line->setSubTotal(0);
                $line->setTaxTotal(0);
                $line->setNetPrice(0);
                $line->setDescription('Free trial');
                $line->setSubscription($subscription);
                $lines[] = $line;
            }
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
            $line->setNetPrice($priceInfo->netPrice->getMinorAmount()->toInt());

            $line->setConvertedTotal($this->toSystemConverter->convert($priceInfo->total)->getMinorAmount()->toInt());
            $line->setConvertedSubTotal($this->toSystemConverter->convert($priceInfo->subTotal)->getMinorAmount()->toInt());
            $line->setConvertedTaxTotal($this->toSystemConverter->convert($priceInfo->vat)->getMinorAmount()->toInt());
            $line->setConvertedNetPrice($this->toSystemConverter->convert($priceInfo->netPrice)->getMinorAmount()->toInt());

            $line->setInvoice($invoice);
            $line->setDescription($lineItem->getDescription());
            $line->setTaxPercentage($priceInfo->taxInfo->rate);
            $line->setTaxType($lineItem->getTaxType());
            $line->setTaxCountry($priceInfo->taxInfo->country);
            $line->setTaxState($priceInfo->taxInfo->state);
            $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);

            $lines[] = $line;
        }

        if (!$total || $total->isZero()) {
            throw new NothingToInvoiceException('Nothing to invoice');
        }

        $line = current($lines);

        return $this->finaliseInvoice($customer, $invoice, $total, $lines, $subTotal, $line->getCurrency(), $vat, $createdAt);
    }

    protected function buildForPrice(
        Subscription $subscription,
        SubscriptionPlan $plan,
        Price $price,
        Customer $customer,
        ?\DateTime $createdAt,
        Invoice $invoice,
        mixed $total,
        mixed $subTotal,
        mixed $vat,
        array $lines,
        ?Money $money = null,
    ): array {
        $lastValue = null;
        $taxType = $plan->getProduct()->getTaxType();
        $invoicedMetricCounter = null;
        if ($price->getUsage()) {
            $metricCounter = $this->metricUsageRepository->getForCustomerAndMetric($customer, $price->getMetric());
            $invoicedMetricCounter = new InvoicedMetricCounter();
            $invoicedMetricCounter->setMetricCounter($metricCounter);
            $invoicedMetricCounter->setMetric($metricCounter->getMetric());
            $invoicedMetricCounter->setCreatedAt($createdAt);
            $invoicedMetricCounter->setInvoice($invoice);

            if (MetricType::RESETTABLE === $price->getMetricType()) {
                $metricCounter->setValue(0);
                $usage = $this->metricProvider->getMetric($subscription);
            } else {
                $lastInvoice = $this->invoiceRepository->getLastForCustomer($customer);
                $totalUsage = $this->metricProvider->getMetric($subscription);
                $lastValue = $lastInvoice?->getInvoiceMetricForMetricCounter($metricCounter)?->getValue() ?? 0.0;
                $usage = $totalUsage + $lastValue;
                $metricCounter->setValue($usage);
            }

            $invoicedMetricCounter->setValue($usage);
            $metricCounter->setUpdatedAt($createdAt);
            $this->metricUsageRepository->save($metricCounter);
        } else {
            $usage = $subscription->getSeats();
            $usage = $this->quantityProvider->getQuantity($usage, $createdAt, $subscription);
        }
        if (!$money) {
            // Pass Metric Usage
            $priceInfos = $this->pricer->getCustomerPriceInfo($price, $customer, $taxType, $usage, $lastValue);
        } else {
            $priceInfos = [$this->pricer->getCustomerPriceInfoFromMoney($money, $customer, $price->isIncludingTax(), $taxType)];
        }

        $priceInfo = null;

        foreach ($priceInfos as $priceInfo) {
            $total = $total?->plus($priceInfo->total) ?? $priceInfo->total;
            $subTotal = $subTotal?->plus($priceInfo->subTotal) ?? $priceInfo->subTotal;
            $vat = $vat?->plus($priceInfo->vat) ?? $priceInfo->vat;
            $line = new InvoiceLine();
            $line->setInvoicedMetricCounter($invoicedMetricCounter);
            $line->setInvoice($invoice);
            $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
            $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $line->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setNetPrice($priceInfo->netPrice->getMinorAmount()->toInt());

            $line->setConvertedTotal($this->toSystemConverter->convert($priceInfo->total)->getMinorAmount()->toInt());
            $line->setConvertedSubTotal($this->toSystemConverter->convert($priceInfo->subTotal)->getMinorAmount()->toInt());
            $line->setConvertedTaxTotal($this->toSystemConverter->convert($priceInfo->vat)->getMinorAmount()->toInt());
            $line->setConvertedNetPrice($this->toSystemConverter->convert($priceInfo->netPrice)->getMinorAmount()->toInt());

            $line->setQuantity($priceInfo->quantity);
            if ($priceInfo->quantity > 1) {
                $line->setDescription(sprintf('%d x %s', $priceInfo->quantity, $subscription->getPlanName()));
            } else {
                $line->setDescription($subscription->getPlanName());
            }
            $line->setTaxPercentage($priceInfo->taxInfo->rate);
            $line->setTaxType($taxType);
            $line->setTaxCountry($priceInfo->taxInfo->country);
            $line->setTaxState($priceInfo->taxInfo->state);
            $line->setReverseCharge($priceInfo->taxInfo->reverseCharge);
            $line->setProduct($subscription->getSubscriptionPlan()->getProduct());
            $line->setMetadata($subscription->getMetadata());
            $line->setSubscription($subscription);

            $lines[] = $line;
        }

        return [$priceInfo, $total, $subTotal, $vat, $lines];
    }

    /**
     * @throws MoneyMismatchException
     */
    protected function finaliseInvoice(Customer $customer, Invoice $invoice, ?Money $total, array $lines, ?Money $subTotal, string $currencyCode, ?Money $vat, \DateTime $createdAt): Invoice
    {
        if ($customer->hasCredit() && !$customer->getCreditAsMoney()->isZero()) {
            $line = new InvoiceLine();
            $line->setCurrency($customer->getCreditCurrency());
            $line->setInvoice($invoice);
            $line->setTaxTotal(0);
            $line->setTaxPercentage(0);
            $line->setConvertedTaxTotal(0);
            $credit = $customer->getCreditAsMoney();
            $description = 'Credit adjustment';

            if ($total->getCurrency() !== $credit->getCurrency()) {
                $description = ' - converted from '.$credit;
                $credit = $this->currencyConverter->convert($credit, $total->getCurrency(), RoundingMode::HALF_DOWN);
            }

            if ($credit->isPositive()) {
                $amount = $credit->negated();
                if ($total->plus($amount)->isPositive()) {
                    $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_DEBIT, $customer, $amount->abs());

                    $customer->setCreditAmount(null);
                    $customer->setCreditCurrency(null);
                } else {
                    $minus = $credit->minus($total);
                    $amount = $amount->plus($minus);

                    $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_DEBIT, $customer, $amount->abs());
                    $customer->addCreditAsMoney($amount);
                }
            } else {
                $amount = $credit->abs();
                $customer->setCreditAmount(null);
                $customer->setCreditCurrency(null);
                $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_CREDIT, $customer, $amount);
            }

            $line->setTotal($amount->getMinorAmount()->toInt());
            $line->setSubTotal($amount->getMinorAmount()->toInt());
            $line->setNetPrice($amount->getMinorAmount()->toInt());

            $line->setConvertedTotal($amount->getMinorAmount()->toInt());
            $line->setConvertedSubTotal($amount->getMinorAmount()->toInt());
            $line->setConvertedNetPrice($amount->getMinorAmount()->toInt());

            $line->setDescription($description);
            $lines[] = $line;
            $total = $total?->plus($amount, RoundingMode::HALF_CEILING) ?? $amount;
            $subTotal = $subTotal?->plus($amount, RoundingMode::HALF_CEILING) ?? $amount;
        }

        try {
            $voucherApplication = $this->voucherApplicationRepository->findUnUsedForCustomer($customer);

            $line = new InvoiceLine();
            $line->setCurrency($currencyCode);
            $line->setInvoice($invoice);

            $percentage = $voucherApplication->getVoucher()->getPercentage();
            $percentage /= 100;
            $amount = $total->multipliedBy($percentage, RoundingMode::HALF_CEILING)->negated();

            $vatAmount = $vat->multipliedBy($percentage, RoundingMode::HALF_CEILING)->negated();

            $line->setDescription($voucherApplication->getVoucher()->getName());
            $line->setTaxPercentage($vatAmount->getMinorAmount()->toInt());

            $line->setSubTotal($amount->getMinorAmount()->toInt());
            $line->setTotal($amount->getMinorAmount()->toInt());
            $line->setNetPrice($amount->getMinorAmount()->toInt());

            $line->setConvertedTotal($amount->getMinorAmount()->toInt());
            $line->setConvertedSubTotal($amount->getMinorAmount()->toInt());
            $line->setConvertedNetPrice($amount->getMinorAmount()->toInt());

            $line->setTaxTotal(0);

            $vat = $vat?->plus($vatAmount, RoundingMode::HALF_CEILING);
            $total = $total->plus($amount, RoundingMode::HALF_CEILING);
            $subTotal = $subTotal->plus($amount, RoundingMode::HALF_CEILING);

            $lines[] = $line;
            $voucherApplication->setUsed(true);
            $this->voucherApplicationRepository->save($voucherApplication);
        } catch (NoEntityFoundException $e) {
        }

        $invoice->setCurrency($currencyCode);
        $invoice->setLines($lines);
        $invoice->setTaxTotal($vat?->getMinorAmount()?->toInt() ?? 0);
        $invoice->setTotal($total?->getMinorAmount()?->toInt() ?? 0);
        $invoice->setAmountDue($total?->getMinorAmount()?->toInt() ?? 0);
        $invoice->setSubTotal($subTotal?->getMinorAmount()?->toInt() ?? 0);

        $invoice->setConvertedTaxTotal($this->toSystemConverter->convert($vat ?? Money::zero('usd'))->getMinorAmount()->toInt() ?? 0);
        $invoice->setConvertedTotal($this->toSystemConverter->convert($total ?? Money::zero('usd'))->getMinorAmount()?->toInt() ?? 0);
        $invoice->setConvertedAmountDue($this->toSystemConverter->convert($total ?? Money::zero('usd'))->getMinorAmount()?->toInt() ?? 0);
        $invoice->setConvertedSubTotal($this->toSystemConverter->convert($subTotal ?? Money::zero('usd'))->getMinorAmount()?->toInt() ?? 0);

        $invoice->setPaid(false);
        $invoice->setCreatedAt($createdAt);
        $invoice->setUpdatedAt($createdAt);
        $invoice->setCustomer($customer);
        $invoice->setPayeeAddress($customer->getBillingAddress());
        $invoice->setBillerAddress($customer->getBrandSettings()->getAddress());

        if (0 === $invoice->getTotal()) {
            $invoice->setPaid(true);
        }

        $this->dateDecider->setDueAt($invoice);

        $this->invoiceRepository->save($invoice);

        $this->eventDispatcher->dispatch(new InvoiceCreated($invoice), InvoiceCreated::NAME);

        return $invoice;
    }
}
