<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Quotes;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Entity\Quote;
use App\Invoice\Number\InvoiceNumberGeneratorProvider;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\QuoteRepositoryInterface;
use App\Subscription\SubscriptionFactory;
use Brick\Money\Money;

class QuoteConverter
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private SubscriptionFactory $subscriptionFactory,
        private QuoteRepositoryInterface $quoteRepository,
        private InvoiceNumberGeneratorProvider $provider,
    ) {
    }

    public function convertToInvoice(Quote $quote): Invoice
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
                $subscription = $this->subscriptionFactory->create($customer, $plan, $price);
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

        $this->quoteRepository->save($quote);
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
