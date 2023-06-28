<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
