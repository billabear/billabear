<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\ChargeBackServiceInterface;
use Obol\CreditServiceInterface;
use Obol\CustomerServiceInterface;
use Obol\HostedCheckoutServiceInterface;
use Obol\InvoiceServiceInterface;
use Obol\PaymentMethodServiceInterface;
use Obol\PaymentServiceInterface;
use Obol\PriceServiceInterface;
use Obol\ProductServiceInterface;
use Obol\Provider\ProviderInterface;
use Obol\RefundServiceInterface;
use Obol\SubscriptionServiceInterface;
use Obol\VoucherServiceInterface;
use Obol\WebhookServiceInterface;

class Provider implements ProviderInterface
{
    public function payments(): PaymentServiceInterface
    {
        return new PaymentService();
    }

    public function hostedCheckouts(): HostedCheckoutServiceInterface
    {
        return new HostedCheckoutService();
    }

    public function customers(): CustomerServiceInterface
    {
        return new CustomerService();
    }

    public function prices(): PriceServiceInterface
    {
        return new PriceService();
    }

    public function products(): ProductServiceInterface
    {
        return new ProductService();
    }

    public function refunds(): RefundServiceInterface
    {
        return new RefundService();
    }

    public function getName(): string
    {
        return 'test_dummy';
    }

    public function subscriptions(): SubscriptionServiceInterface
    {
        return new SubscriptionService();
    }

    public function webhook(): WebhookServiceInterface
    {
        return new WebhookService();
    }

    public function invoices(): InvoiceServiceInterface
    {
        return new InvoiceService();
    }

    public function chargeBacks(): ChargeBackServiceInterface
    {
        return new ChargeBackService();
    }

    public function paymentMethods(): PaymentMethodServiceInterface
    {
        // TODO: Implement paymentMethods() method.
    }

    public function credit(): CreditServiceInterface
    {
        return new CreditService();
    }

    public function vouchers(): VoucherServiceInterface
    {
        return new VoucherService();
    }
}
