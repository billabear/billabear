<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Quotes;

use App\Entity\ConvertableToInvoiceInterface;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Invoice\Number\InvoiceNumberGeneratorProvider;
use App\Repository\InvoiceRepositoryInterface;
use App\Subscription\SubscriptionFactory;
use Brick\Money\Money;

class QuoteConverter
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private SubscriptionFactory $subscriptionFactory,
        private InvoiceNumberGeneratorProvider $provider,
    ) {
    }

    public function convertToInvoice(ConvertableToInvoiceInterface $quote): Invoice
    {
        $customer = $quote->getCustomer();
        $subscriptions = [];
        $invoiceLines = [];

        $invoice = new Invoice();
        $invoice->setCustomer($customer);
        $invoice->setCreatedAt(new \DateTime());
        $invoice->setUpdatedAt(new \DateTime());
        $invoice->setCurrency($quote->getCurrency());

        $total = Money::zero($quote->getCurrency());
        $subTotal = Money::zero($quote->getCurrency());
        $taxTotal = Money::zero($quote->getCurrency());
        foreach ($quote->getLines() as $line) {
            $plan = $line->getSubscriptionPlan();
            $price = $line->getPrice();

            $invoiceLine = new InvoiceLine();

            if ($plan) {
                $subscription = $this->subscriptionFactory->create($customer, $plan, $price, seatNumbers: $line->getSeatNumber());
                $subscriptions[] = $subscription;
                $invoiceLine->setDescription($subscription->getPlanName());
            } else {
                $invoiceLine->setDescription($line->getDescription());
            }

            $invoiceLine->setInvoice($invoice);
            $invoiceLine->setCurrency($line->getCurrency());
            $invoiceLine->setTotal($line->getTotal());
            $invoiceLine->setSubTotal($line->getSubTotal());
            $invoiceLine->setTaxTotal($line->getTaxTotal());
            $invoiceLine->setTaxType($line->getTaxType());
            $invoiceLine->setTaxPercentage($line->getTaxPercentage());
            $invoiceLine->setReverseCharge($line->isReverseCharge());
            $invoiceLine->setTaxCountry($line->getTaxCountry());

            $total = $this->addAmount($total, $invoiceLine->getTotalMoney());
            $subTotal = $this->addAmount($subTotal, $invoiceLine->getSubTotalMoney());
            $taxTotal = $this->addAmount($taxTotal, $invoiceLine->getVatTotalMoney());

            $invoiceLines[] = $invoiceLine;
        }

        $numbrer = $this->provider->getGenerator()->generate();

        $invoice->setInvoiceNumber($numbrer);
        $invoice->setLines($invoiceLines);
        $invoice->setTotal($total->getMinorAmount()->toInt());
        $invoice->setSubTotal($subTotal->getMinorAmount()->toInt());
        $invoice->setTaxTotal($taxTotal->getMinorAmount()->toInt());
        $invoice->setAmountDue($total->getMinorAmount()->toInt());
        $invoice->setValid(true);
        $invoice->setPayeeAddress($customer->getBillingAddress());
        $invoice->setBillerAddress($customer->getBrandSettings()->getAddress());

        $quote->setSubscriptions($subscriptions);
        $quote->setUpdatedAt(new \DateTime('now'));
        $quote->setPaid(true);
        $quote->setPaidAt(new \DateTime('now'));

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }

    protected function addAmount(?Money $originalAmount, Money $additionalMoney): Money
    {
        if (!$originalAmount) {
            return $additionalMoney;
        }

        return $originalAmount->plus($additionalMoney);
    }
}
