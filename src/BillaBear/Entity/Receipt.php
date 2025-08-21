<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('receipt')]
class Receipt extends \Parthenon\Billing\Entity\Receipt
{
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedSubTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTaxTotal = null;

    #[ORM\ManyToOne(targetEntity: Payment::class)]
    private ?Payment $payment = null;

    public function getConvertedTotal(): ?int
    {
        return $this->convertedTotal;
    }

    public function setConvertedTotal(?int $convertedTotal): void
    {
        $this->convertedTotal = $convertedTotal;
    }

    public function getConvertedSubTotal(): ?int
    {
        return $this->convertedSubTotal;
    }

    public function setConvertedSubTotal(?int $convertedSubTotal): void
    {
        $this->convertedSubTotal = $convertedSubTotal;
    }

    public function getConvertedTaxTotal(): ?int
    {
        return $this->convertedTaxTotal;
    }

    public function setConvertedTaxTotal(?int $convertedTaxTotal): void
    {
        $this->convertedTaxTotal = $convertedTaxTotal;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): void
    {
        $this->payment = $payment;
    }
}
