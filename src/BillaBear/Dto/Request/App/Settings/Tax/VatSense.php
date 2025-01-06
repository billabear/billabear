<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\Tax;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class VatSense
{
    #[SerializedName('validate_vat_ids')]
    public $validateVatIds;
    #[SerializedName('vat_sense_enabled')]
    private $vatSenseEnabled;

    #[Assert\Type('string')]
    #[SerializedName('vat_sense_api_key')]
    private $vatSenseApiKey;

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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->vatSenseEnabled && empty($this->vatSenseApiKey)) {
            $context->buildViolation('Cannot be blank')
                ->atPath('vatSenseApiKey')
                ->addViolation();
        }
    }
}
