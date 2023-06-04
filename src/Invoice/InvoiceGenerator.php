<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Credit\CreditAdjustmentRecorder;
use App\Entity\Credit;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\VoucherApplicationRepositoryInterface;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;

class InvoiceGenerator
{
    public function __construct(
        private PricerInterface $pricer,
        private InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        private InvoiceRepositoryInterface $invoiceRepository,
        private CreditAdjustmentRecorder $creditAdjustmentRecorder,
        private VoucherApplicationRepositoryInterface $voucherApplicationRepository,
    ) {
    }

    /**
     * @param Subscription[] $subscriptions
     */
    public function generateForCustomerAndSubscriptions(Customer $customer, array $subscriptions): Invoice
    {
        if (empty($subscriptions)) {
            throw new \Exception("Can't generate invoices for no subscription");
        }

        $lines = [];
        $total = null;
        $subTotal = null;
        $vat = null;
        $invoice = new Invoice();
        $invoice->setValid(true);
        $invoice->setInvoiceNumber($this->invoiceNumberGenerator->generate());

        foreach ($subscriptions as $subscription) {
            $price = $subscription->getPrice();

            if (!$price instanceof Price) {
                throw new \Exception(sprintf("The subscription '%s' has no price", $subscription->getPlanName()));
            }

            $priceInfo = $this->pricer->getCustomerPriceInfo($price, $customer);

            $total = $total?->plus($priceInfo->total) ?? $priceInfo->total;
            $subTotal = $subTotal?->plus($priceInfo->subTotal) ?? $priceInfo->subTotal;
            $vat = $vat?->plus($priceInfo->vat) ?? $priceInfo->vat;

            $line = new InvoiceLine();
            $line->setCurrency($priceInfo->total->getCurrency()->getCurrencyCode());
            $line->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $line->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $line->setVatTotal($priceInfo->vat->getMinorAmount()->toInt());
            $line->setInvoice($invoice);
            $line->setDescription($subscription->getPlanName());
            $line->setVatPercentage($priceInfo->taxRate);
            $lines[] = $line;
        }
        if ($customer->hasCredit() && !$customer->getCreditAsMoney()->isZero()) {
            $line = new InvoiceLine();
            $line->setCurrency($customer->getCreditCurrency());
            $line->setInvoice($invoice);
            $line->setVatTotal(0);
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
            $percentage = $percentage / 100;
            $amount = $total->multipliedBy($percentage)->negated();

            $vatAmount = $vat->multipliedBy($percentage)->negated();

            $line->setDescription($voucherApplication->getVoucher()->getName());
            $line->setVatPercentage($vatAmount->getMinorAmount()->toInt());
            $line->setSubTotal($amount->getMinorAmount()->toInt());
            $line->setTotal($amount->getMinorAmount()->toInt());
            $line->setVatTotal(0);

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
        $invoice->setVatTotal($vat->getMinorAmount()->toInt());
        $invoice->setTotal($total->getMinorAmount()->toInt());
        $invoice->setAmountDue($total->getMinorAmount()->toInt());
        $invoice->setSubTotal($subTotal->getMinorAmount()->toInt());
        $invoice->setPaid(false);
        $invoice->setCreatedAt(new \DateTime('now'));
        $invoice->setUpdatedAt(new \DateTime('now'));
        $invoice->setSubscriptions($subscriptions);
        $invoice->setCustomer($customer);
        $invoice->setPayeeAddress($customer->getBillingAddress());
        $invoice->setBillerAddress($customer->getBrandSettings()->getAddress());

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }
}
