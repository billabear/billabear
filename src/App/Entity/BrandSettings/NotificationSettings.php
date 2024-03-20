<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity\BrandSettings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class NotificationSettings
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $subscriptionCreation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $subscriptionCancellation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $expiringCardWarning = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $expiringCardDayBefore = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $invoiceCreated = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $invoiceOverdue = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $quoteCreated = null;

    public function getSubscriptionCreation(): bool
    {
        return true === $this->subscriptionCreation;
    }

    public function setSubscriptionCreation(?bool $subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }

    public function getSubscriptionCancellation(): bool
    {
        return true === $this->subscriptionCancellation;
    }

    public function setSubscriptionCancellation(?bool $subscriptionCancellation): void
    {
        $this->subscriptionCancellation = $subscriptionCancellation;
    }

    public function getExpiringCardWarning(): bool
    {
        return true === $this->expiringCardWarning;
    }

    public function setExpiringCardWarning(?bool $expiringCardWarning): void
    {
        $this->expiringCardWarning = $expiringCardWarning;
    }

    public function getExpiringCardDayBefore(): bool
    {
        return true === $this->expiringCardDayBefore;
    }

    public function setExpiringCardDayBefore(?bool $expiringCardDayBefore): void
    {
        $this->expiringCardDayBefore = $expiringCardDayBefore;
    }

    public function getInvoiceCreated(): bool
    {
        return true === $this->invoiceCreated;
    }

    public function setInvoiceCreated(?bool $invoiceCreated): void
    {
        $this->invoiceCreated = $invoiceCreated;
    }

    public function getQuoteCreated(): bool
    {
        return true === $this->quoteCreated;
    }

    public function setQuoteCreated(?bool $quoteCreated): void
    {
        $this->quoteCreated = $quoteCreated;
    }

    public function getInvoiceOverdue(): ?bool
    {
        return true === $this->invoiceOverdue;
    }

    public function setInvoiceOverdue(?bool $invoiceOverdue): void
    {
        $this->invoiceOverdue = $invoiceOverdue;
    }
}
