<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\PaymentCard;

#[ORM\Entity]
#[ORM\Index(name: 'threshold_idx', columns: ['country', 'created_at'])]
#[ORM\Table('payment')]
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $accountingReference = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedAmount = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $convertedCurrency = null;

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

    public function getAccountingReference(): ?string
    {
        return $this->accountingReference;
    }

    public function setAccountingReference(?string $accountingReference): void
    {
        $this->accountingReference = $accountingReference;
    }

    public function getConvertedAmount(): ?int
    {
        return $this->convertedAmount;
    }

    public function setConvertedAmount(?int $convertedAmount): void
    {
        $this->convertedAmount = $convertedAmount;
    }

    public function getConvertedCurrency(): ?string
    {
        return $this->convertedCurrency;
    }

    public function setConvertedCurrency(?string $convertedCurrency): void
    {
        $this->convertedCurrency = $convertedCurrency;
    }

    public function setConvertedMoney(Money $money): void
    {
        $this->convertedAmount = $money->getMinorAmount()->toInt();
        $this->convertedCurrency = $money->getCurrency()->getCurrencyCode();
    }
}
