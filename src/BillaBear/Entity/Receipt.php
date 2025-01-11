<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    public function getConvertedAmountDue(): ?int
    {
        return $this->convertedAmountDue;
    }

    public function setConvertedAmountDue(?int $convertedAmountDue): void
    {
        $this->convertedAmountDue = $convertedAmountDue;
    }

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
}
