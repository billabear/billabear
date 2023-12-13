<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Dto\Request\App\Invoice\CreateInvoice;
use App\Dto\Request\App\Invoice\CreateInvoiceItem;
use App\Dto\Request\App\Invoice\CreateInvoiceSubscription;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Enum\TaxType;
use App\Payment\InvoiceCharger;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
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
            $money = Money::ofMinor($item->getAmount(), $item->getCurrency());
            $lineItem = new LineItem();
            $lineItem->setMoney($money);
            $lineItem->setDescription($item->getDescription());
            $lineItem->setIncludeTax($item->getIncludeTax());
            $lineItem->setTaxType(TaxType::fromName($item->getTaxType()));

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
