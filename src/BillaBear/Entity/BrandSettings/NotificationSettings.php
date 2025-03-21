<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\BrandSettings;

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

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $sendTrialEndingWarning = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sendBeforeChargeWarnings = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $paymentFailure = null;

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

    public function getSendTrialEndingWarning(): bool
    {
        return true === $this->sendTrialEndingWarning;
    }

    public function setSendTrialEndingWarning(?bool $sendTrialEndingWarning): void
    {
        $this->sendTrialEndingWarning = $sendTrialEndingWarning;
    }

    public function getSendBeforeChargeWarnings(): string
    {
        if (null === $this->sendBeforeChargeWarnings) {
            return 'none';
        }

        return $this->sendBeforeChargeWarnings;
    }

    public function setSendBeforeChargeWarnings(?string $sendBeforeChargeWarnings): void
    {
        $this->sendBeforeChargeWarnings = $sendBeforeChargeWarnings;
    }

    public function getPaymentFailure(): bool
    {
        return true === $this->paymentFailure;
    }

    public function setPaymentFailure(?bool $paymentFailure): void
    {
        $this->paymentFailure = $paymentFailure;
    }
}
