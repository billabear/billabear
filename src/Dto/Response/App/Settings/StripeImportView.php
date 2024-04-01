<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class StripeImportView
{
    #[SerializedName('stripe_imports')]
    private array $stripeImports = [];

    #[SerializedName('use_stripe_billing')]
    private bool $useStripeBilling;

    #[SerializedName('webhook_url')]
    private ?string $webhookUrl;

    public function getStripeImports(): array
    {
        return $this->stripeImports;
    }

    public function setStripeImports(array $stripeImports): void
    {
        $this->stripeImports = $stripeImports;
    }

    public function isUseStripeBilling(): bool
    {
        return $this->useStripeBilling;
    }

    public function setUseStripeBilling(bool $useStripeBilling): void
    {
        $this->useStripeBilling = $useStripeBilling;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }
}
