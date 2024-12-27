<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Payment;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;

class SyncPayment
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function sync(Payment $payment): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $paymentService = $integration->getPaymentService();
        $invoiceService = $integration->getInvoiceService();

        if (!$payment->getAccountingReference() && !$invoiceService->isPaid($payment->getInvoice())) {
            $registration = $paymentService->register($payment);
            $payment->setAccountingReference($registration->paymentReference);
            $this->paymentRepository->save($payment);
        }
    }
}
