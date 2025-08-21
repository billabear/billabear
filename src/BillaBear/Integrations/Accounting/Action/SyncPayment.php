<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Payment;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\AccountingIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class SyncPayment
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
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

        try {
            if (!$payment->getAccountingReference() && !$invoiceService->isPaid($payment->getInvoice())) {
                $registration = $paymentService->register($payment);
                $payment->setAccountingReference($registration->paymentReference);
                $this->paymentRepository->save($payment);
            }
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new AccountingIntegrationFailure($e));

            throw $e;
        }
    }
}
