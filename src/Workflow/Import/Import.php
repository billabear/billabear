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

namespace App\Workflow\Import;

use App\Entity\StripeImport;
use App\Import\Stripe\ChargeBackImporter;
use App\Import\Stripe\CustomerImporter;
use App\Import\Stripe\PaymentImporter;
use App\Import\Stripe\PriceImporter;
use App\Import\Stripe\ProductImporter;
use App\Import\Stripe\RefundImporter;
use App\Import\Stripe\SubscriptionImporter;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

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
        ];
    }
}