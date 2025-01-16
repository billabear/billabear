<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Dto\Request\App\Invoice\CreateInvoice;
use BillaBear\Dto\Request\App\Invoice\CreateInvoiceItem;
use BillaBear\Dto\Request\App\Invoice\CreateInvoiceSubscription;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use BillaBear\Subscription\SubscriptionFactory;
use Brick\Money\Money;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class ManualInvoiceCreator
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionFactory $subscriptionManager,
        private InvoiceGenerator $invoiceGenerator,
        private InvoiceCharger $invoiceCharger,
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
            /** @var Price $price */
            $price = $this->priceRepository->getById($subscription->getPrice());
            $subscription = $this->subscriptionManager->create($customer, $plan, $price, seatNumber: $subscription->getSeatNumber());
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
            try {
                $this->invoiceCharger->chargeInvoice($invoice);
            } catch (PaymentFailureException $e) {
                $this->getLogger()->warning(
                    'Attempted to charge manually created invoice failed',
                    ['reason' => $e->getReason()->value]
                );
            }
        }

        return $invoice;
    }
}
