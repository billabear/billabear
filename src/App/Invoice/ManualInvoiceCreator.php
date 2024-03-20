<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice;

use App\Dto\Request\App\Invoice\CreateInvoice;
use App\Dto\Request\App\Invoice\CreateInvoiceItem;
use App\Dto\Request\App\Invoice\CreateInvoiceSubscription;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Payment\InvoiceCharger;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use App\Subscription\SubscriptionFactory;
use Brick\Money\Money;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;

class ManualInvoiceCreator
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionFactory $subscriptionManager,
        private InvoiceGenerator $invoiceGenerator,
        private InvoiceCharger $invoiceCharger,
        private DueDateDecider $dateDecider,
        private InvoiceRepositoryInterface $invoiceRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
    ) {
    }

    public function createInvoice(CreateInvoice $createInvoice): Invoice
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->getById($createInvoice->getCustomer());

        $subscriptions = [];
        /** @var CreateInvoiceSubscription $subscription */
        foreach ($createInvoice->getSubscriptions() as $subscription) {
            /** @var SubscriptionPlan $plan */
            $plan = $this->subscriptionPlanRepository->getById($subscription->getPlan());
            /** @var \Parthenon\Billing\Entity\Price $price */
            $price = $this->priceRepository->getById($subscription->getPrice());
            $subscription = $this->subscriptionManager->create($customer, $plan, $price, seatNumbers: $subscription->getSeatNumber());
            $subscriptions[] = $subscription;
        }

        $lines = [];
        /** @var CreateInvoiceItem $item */
        foreach ($createInvoice->getItems() as $item) {
            $taxType = $this->taxTypeRepository->findById($item->getTaxType());
            $money = Money::ofMinor($item->getAmount(), $item->getCurrency());
            $lineItem = new LineItem();
            $lineItem->setMoney($money);
            $lineItem->setDescription($item->getDescription());
            $lineItem->setIncludeTax($item->getIncludeTax());
            $lineItem->setTaxType($taxType);

            $lines[] = $lineItem;
        }

        $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $subscriptions, $lines);

        if (null !== $createInvoice->getDueDate()) {
            $invoice->setDueAt(new \DateTime($createInvoice->getDueDate()));
            $this->invoiceRepository->save($invoice);
        }

        if (Customer::BILLING_TYPE_CARD === $customer->getBillingType()) {
            $this->invoiceCharger->chargeInvoice($invoice);
        }

        return $invoice;
    }
}
