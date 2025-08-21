<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Quotes;

use BillaBear\Entity\ConvertableToInvoiceInterface;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Event\Invoice\InvoiceCreated;
use BillaBear\Invoice\Number\InvoiceNumberGeneratorProvider;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Subscription\SubscriptionFactory;
use Brick\Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class QuoteConverter
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private SubscriptionFactory $subscriptionFactory,
        private InvoiceNumberGeneratorProvider $provider,
        private EventDispatcherInterface $eventDispatcher,
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
                $subscription = $this->subscriptionFactory->create($customer, $plan, $price, seatNumber: $line->getSeatNumber());
                $subscriptions[] = $subscription;
                $invoiceLine->setDescription($subscription->getPlanName());
            } else {
                $invoiceLine->setDescription($line->getDescription());
            }

            $invoiceLine->setInvoice($invoice);
            $invoiceLine->setCurrency($line->getCurrency());
            $invoiceLine->setTotal($line->getTotal());
            $invoiceLine->setSubTotal($line->getSubTotal());
            $invoiceLine->setNetPrice($line->getSubTotal());
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

        $number = $this->provider->getGenerator()->generate();

        $invoice->setInvoiceNumber($number);
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
        $this->eventDispatcher->dispatch(new InvoiceCreated($invoice), InvoiceCreated::NAME);

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
