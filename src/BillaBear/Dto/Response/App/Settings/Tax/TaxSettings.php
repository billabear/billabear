<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Settings\Tax;

use Symfony\Component\Serializer\Annotation\SerializedName;

class TaxSettings
{
    #[SerializedName('tax_customers_with_tax_number')]
    private bool $taxCustomersWithTaxNumber;

    #[SerializedName('eu_business_tax_rules')]
    private bool $euBusinessTaxRules;

    #[SerializedName('eu_one_stop_shop_rule')]
    private bool $euOneStopShopRule;

    #[SerializedName('vat_sense_enabled')]
    private $vatSenseEnabled;

    #[SerializedName('vat_sense_api_key')]
    private $vatSenseApiKey;

    #[SerializedName('validate_vat_ids')]
    public $validateVatIds;

    public function getTaxCustomersWithTaxNumber(): bool
    {
        return $this->taxCustomersWithTaxNumber;
    }

    public function setTaxCustomersWithTaxNumber(bool $taxCustomersWithTaxNumber): void
    {
        $this->taxCustomersWithTaxNumber = $taxCustomersWithTaxNumber;
    }

    public function isEuBusinessTaxRules(): bool
    {
        return $this->euBusinessTaxRules;
    }

    public function setEuBusinessTaxRules(bool $euBusinessTaxRules): void
    {
        $this->euBusinessTaxRules = $euBusinessTaxRules;
    }

    public function isEuOneStopShopRule(): bool
    {
        return $this->euOneStopShopRule;
    }

    public function setEuOneStopShopRule(bool $euOneStopShopRule): void
    {
        $this->euOneStopShopRule = $euOneStopShopRule;
    }

    public function getVatSenseEnabled()
    {
        return $this->vatSenseEnabled;
    }

    public function setVatSenseEnabled($vatSenseEnabled): void
    {
        $this->vatSenseEnabled = $vatSenseEnabled;
    }

    public function getVatSenseApiKey()
    {
        return $this->vatSenseApiKey;
    }

    public function setVatSenseApiKey($vatSenseApiKey): void
    {
        $this->vatSenseApiKey = $vatSenseApiKey;
    }

    public function getValidateVatIds()
    {
        return $this->validateVatIds;
    }

    public function setValidateVatIds($validateVatIds): void
    {
        $this->validateVatIds = $validateVatIds;
    }
}
