<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\PdfTemplates;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
readonly class UpdateGeneratorSettings
{
    public function __construct(
        #[Assert\Choice(choices: ['mpdf', 'wkhtmltopdf', 'docraptor'])]
        #[Assert\NotBlank]
        public ?string $generator = null,
        #[SerializedName('tmp_dir')]
        public ?string $tmpDir = null,
        #[SerializedName('api_key')]
        public ?string $apiKey = null,
        public ?string $bin = null,
    ) {
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ('mpdf' === $this->generator) {
            if (!isset($this->tmpDir) || empty($this->tmpDir)) {
                $context->buildViolation('must have tmp dir')->atPath('tmp_dir')->addViolation();
            }
        }

        if ('wkhtmltopdf' === $this->generator) {
            if (!isset($this->bin) || empty($this->bin)) {
                $context->buildViolation('must have bin')->atPath('bin')->addViolation();
            }
        }

        if ('docraptor' === $this->generator) {
            if (!isset($this->apiKey) || empty($this->apiKey)) {
                $context->buildViolation('must have api key')->atPath('api_key')->addViolation();
            }
        }
    }
}
