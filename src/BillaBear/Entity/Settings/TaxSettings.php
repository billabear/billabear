<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TaxSettings
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $taxCustomersWithTaxNumbers = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $europeanBusinessTaxRules = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $oneStopShopTaxRules = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $validateTaxNumber = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $vatSenseEnabled = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $vatSenseApiKey = null;

    public function getTaxCustomersWithTaxNumbers(): bool
    {
        return true === $this->taxCustomersWithTaxNumbers;
    }

    public function setTaxCustomersWithTaxNumbers(?bool $taxCustomersWithTaxNumbers): void
    {
        $this->taxCustomersWithTaxNumbers = $taxCustomersWithTaxNumbers;
    }

    public function getEuropeanBusinessTaxRules(): bool
    {
        return true === $this->europeanBusinessTaxRules;
    }

    public function setEuropeanBusinessTaxRules(?bool $europeanBusinessTaxRules): void
    {
        $this->europeanBusinessTaxRules = $europeanBusinessTaxRules;
    }

    public function getOneStopShopTaxRules(): bool
    {
        return true === $this->oneStopShopTaxRules;
    }

    public function setOneStopShopTaxRules(?bool $oneStopShopTaxRules): void
    {
        $this->oneStopShopTaxRules = $oneStopShopTaxRules;
    }

    public function getValidateTaxNumber(): bool
    {
        return true === $this->validateTaxNumber;
    }

    public function setValidateTaxNumber(?bool $validateTaxNumber): void
    {
        $this->validateTaxNumber = $validateTaxNumber;
    }

    public function getVatSenseApiKey(): ?string
    {
        return $this->vatSenseApiKey;
    }

    public function setVatSenseApiKey(?string $vatSenseApiKey): void
    {
        $this->vatSenseApiKey = $vatSenseApiKey;
    }

    public function getVatSenseEnabled(): bool
    {
        return true === $this->vatSenseEnabled;
    }

    public function setVatSenseEnabled(?bool $vatSenseEnabled): void
    {
        $this->vatSenseEnabled = $vatSenseEnabled;
    }
}
