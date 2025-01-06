<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

use BillaBear\Entity\Price;
use BillaBear\Entity\Product;
use BillaBear\Entity\Subscription;
use BillaBear\Exception\Invoice\CannotEstimateException;
use BillaBear\Pricing\Pricer;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\Usage\MetricCounterRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Common\LoggerAwareTrait;

class CostEstimator
{
    use LoggerAwareTrait;

    public function __construct(
        private Pricer $pricer,
        private MetricCounterRepositoryInterface $metricCounterRepository,
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    /**
     * @throws CannotEstimateException
     */
    public function getEstimate(Subscription $subscription): CostEstimate
    {
        /** @var Price $price */
        $price = $subscription->getPrice();
        if (!$price instanceof Price) {
            $this->getLogger()->error("Tried to get cost estimate for subscription that doesn't have a price", ['subscription_id' => $subscription->getId()]);
            throw new CannotEstimateException("Tried to get cost estimate for subscription that doesn't have a price");
        }

        if (!$price->getUsage()) {
            $this->getLogger()->error("Tried to get cost estimate for subscription that doesn't have a usage price", ['subscription_id' => $subscription->getId()]);
            throw new CannotEstimateException("Tried to get cost estimate for subscription that doesn't have a usage price");
        }

        $usage = $this->metricCounterRepository->getForCustomerAndMetric($subscription->getCustomer(), $subscription->getPrice()->getMetric());

        $lastValue = null;
        $customer = $subscription->getCustomer();

        if (MetricType::CONTINUOUS === $price->getMetricType()) {
            $lastInvoice = $this->invoiceRepository->getLastForCustomer($customer);
            $lastValue = $lastInvoice?->getInvoicedMetricCounter()?->getValue() ?? 0;
        }

        /** @var Product $plan */
        $product = $subscription->getSubscriptionPlan()->getProduct();
        $priceInfos = $this->pricer->getCustomerPriceInfo($price, $subscription->getCustomer(), $product->getTaxType(), $usage->getValue(), $lastValue);

        $money = Money::zero($subscription->getCurrency());
        foreach ($priceInfos as $priceInfo) {
            $money = $money->plus($priceInfo->total);
        }

        return new CostEstimate($money, $usage->getValue(), $price->getMetric()->getName());
    }

    /**
     * @throws CannotEstimateException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function getTotalEstimate(array $subscriptions): Money
    {
        $money = null;
        foreach ($subscriptions as $subscription) {
            $estimate = $this->getEstimate($subscription);
            if (!$money) {
                $money = $estimate->cost;
            } else {
                $money = $money->plus($estimate->cost);
            }
        }

        return $money;
    }
}
