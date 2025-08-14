<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\Tax;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

readonly class VatSense
{
    public function __construct(
        #[SerializedName('validate_vat_ids')]
        public ?bool $validateVatIds = null,
        #[SerializedName('vat_sense_enabled')]
        public ?bool $vatSenseEnabled = null,
        #[Assert\Type('string')]
        #[SerializedName('vat_sense_api_key')]
        public ?string $vatSenseApiKey = null,
    ) {
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
