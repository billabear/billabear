<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\BrandSettings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Notifications
{
    #[SerializedName('subscription_creation')]
    private bool $subscriptionCreation;

    #[SerializedName('subscription_cancellation')]
    private bool $subscriptionCancellation;

    #[SerializedName('expiring_card_warning')]
    private bool $expiringCardWarning;

    #[SerializedName('expiring_card_warning_day_before')]
    private bool $expiringCardDayBeforeWarning;

    #[SerializedName('invoice_created')]
    private bool $invoiceCreated;

    #[SerializedName('invoice_overdue')]
    private bool $invoiceOverdue;

    #[SerializedName('quote_created')]
    private bool $quoteCreated;

    public function getSubscriptionCreation()
    {
        return true === $this->subscriptionCreation;
    }

    public function setSubscriptionCreation($subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }

    public function getSubscriptionCancellation()
    {
        return true === $this->subscriptionCancellation;
    }

    public function setSubscriptionCancellation($subscriptionCancellation): void
    {
        $this->subscriptionCancellation = $subscriptionCancellation;
    }

    public function getExpiringCardWarning()
    {
        return true === $this->expiringCardWarning;
    }

    public function setExpiringCardWarning($expiringCardWarning): void
    {
        $this->expiringCardWarning = $expiringCardWarning;
    }

    public function getExpiringCardDayBeforeWarning()
    {
        return true === $this->expiringCardDayBeforeWarning;
    }

    public function setExpiringCardDayBeforeWarning($expiringCardDayBeforeWarning): void
    {
        $this->expiringCardDayBeforeWarning = $expiringCardDayBeforeWarning;
    }

    public function isInvoiceCreated(): bool
    {
        return $this->invoiceCreated;
    }

    public function setInvoiceCreated(bool $invoiceCreated): void
    {
        $this->invoiceCreated = $invoiceCreated;
    }

    public function isQuoteCreated(): bool
    {
        return $this->quoteCreated;
    }

    public function setQuoteCreated(bool $quoteCreated): void
    {
        $this->quoteCreated = $quoteCreated;
    }

    public function isInvoiceOverdue(): bool
    {
        return $this->invoiceOverdue;
    }

    public function setInvoiceOverdue(bool $invoiceOverdue): void
    {
        $this->invoiceOverdue = $invoiceOverdue;
    }
}