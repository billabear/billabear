<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncPaymentHandler
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function __invoke(SyncPayment $message)
    {
        $payment = $this->paymentRepository->findById($message->paymentId);
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $paymentService = $integration->getPaymentService();

        if (!$payment->getAccountingReference()) {
            $registration = $paymentService->register($payment);
            $payment->setAccountingReference($registration->paymentReference);
            $this->paymentRepository->save($payment);
        }
    }
}
