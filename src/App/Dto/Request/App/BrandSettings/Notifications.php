<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\BrandSettings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Notifications
{
    #[SerializedName('subscription_creation')]
    private $subscriptionCreation;

    #[SerializedName('subscription_cancellation')]
    private $subscriptionCancellation;

    #[SerializedName('expiring_card_warning')]
    private $expiringCardWarning;

    #[SerializedName('expiring_card_warning_day_before')]
    private $expiringCardDayBeforeWarning;

    #[SerializedName('invoice_created')]
    private $invoiceCreated;

    #[SerializedName('invoice_overdue')]
    private $invoiceOverdue;

    #[SerializedName('quote_created')]
    private $quoteCreated;

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

    public function getInvoiceCreated()
    {
        return $this->invoiceCreated;
    }

    public function setInvoiceCreated($invoiceCreated): void
    {
        $this->invoiceCreated = $invoiceCreated;
    }

    public function getQuoteCreated()
    {
        return $this->quoteCreated;
    }

    public function setQuoteCreated($quoteCreated): void
    {
        $this->quoteCreated = $quoteCreated;
    }

    public function getInvoiceOverdue()
    {
        return true === $this->invoiceOverdue;
    }

    public function setInvoiceOverdue($invoiceOverdue): void
    {
        $this->invoiceOverdue = $invoiceOverdue;
    }
}
