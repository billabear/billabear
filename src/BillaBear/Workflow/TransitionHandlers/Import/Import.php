<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Import;

use BillaBear\Entity\StripeImport;
use BillaBear\Import\Stripe\ChargeBackImporter;
use BillaBear\Import\Stripe\CustomerImporter;
use BillaBear\Import\Stripe\PaymentImporter;
use BillaBear\Import\Stripe\PriceImporter;
use BillaBear\Import\Stripe\ProductImporter;
use BillaBear\Import\Stripe\RefundImporter;
use BillaBear\Import\Stripe\StatsCruncher;
use BillaBear\Import\Stripe\SubscriptionImporter;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class Import implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerImporter $customerImporter,
        private ProductImporter $productImporter,
        private PriceImporter $priceImporter,
        private SubscriptionImporter $subscriptionImporter,
        private PaymentImporter $paymentImporter,
        private RefundImporter $refundImporter,
        private ChargeBackImporter $chargeBackImporter,
        private StatsCruncher $statsCruncher,
    ) {
    }

    public function importCustomers(Event $event)
    {
        $this->getLogger()->info('Import customers');
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->customerImporter->import($stripeImport);
    }

    public function importProducts(Event $event)
    {
        $this->getLogger()->info('Import products');
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->productImporter->import($stripeImport);
    }

    public function importPrices(Event $event)
    {
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->priceImporter->import($stripeImport);
    }

    public function importSubscriptions(Event $event)
    {
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->subscriptionImporter->import($stripeImport);
    }

    public function importPayments(Event $event)
    {
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->paymentImporter->import($stripeImport);
    }

    public function importRefunds(Event $event)
    {
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->refundImporter->import($stripeImport);
    }

    public function importChargeBacks(Event $event)
    {
        /** @var StripeImport $stripeImport */
        $stripeImport = $event->getSubject();
        $this->chargeBackImporter->import($stripeImport);
    }

    public function crunchStats(Event $event)
    {
        $this->statsCruncher->execute();
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.stripe_import.transition.start_customers' => ['importCustomers'],
            'workflow.stripe_import.transition.start_products' => ['importProducts'],
            'workflow.stripe_import.transition.start_prices' => ['importPrices'],
            'workflow.stripe_import.transition.start_subscriptions' => ['importSubscriptions'],
            'workflow.stripe_import.transition.start_payments' => ['importPayments'],
            'workflow.stripe_import.transition.start_refunds' => ['importRefunds'],
            'workflow.stripe_import.transition.start_charge_backs' => ['importChargeBacks'],
            'workflow.stripe_import.transition.crunch_stats' => ['crunchStats'],
        ];
    }
}
