<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\PaymentCard;

#[ORM\Entity]
#[ORM\Table('payment')]
#[ORM\Index(name: 'threshold_idx', columns: ['country', 'created_at'])]
class Payment extends \Parthenon\Billing\Entity\Payment
{
    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private ?Invoice $invoice = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $state = null;

    #[ORM\ManyToOne(targetEntity: PaymentCard::class)]
    private ?PaymentCard $paymentCard = null;

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getPaymentCard(): ?PaymentCard
    {
        return $this->paymentCard;
    }

    public function setPaymentCard(?PaymentCard $paymentCard): void
    {
        $this->paymentCard = $paymentCard;
    }
}
