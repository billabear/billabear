<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Settings\PdfTemplates;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
class UpdateGeneratorSettings
{
    #[Assert\NotBlank()]
    #[Assert\Choice(choices: ['mpdf', 'wkhtmltopdf', 'docraptor'])]
    private $generator;

    #[SerializedName('tmp_dir')]
    private $tmpDir;

    #[SerializedName('api_key')]
    private $apiKey;

    private $bin;

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

    public function getGenerator()
    {
        return $this->generator;
    }

    public function setGenerator($generator): void
    {
        $this->generator = $generator;
    }

    public function getTmpDir()
    {
        return $this->tmpDir;
    }

    public function setTmpDir($tmpDir): void
    {
        $this->tmpDir = $tmpDir;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getBin()
    {
        return $this->bin;
    }

    public function setBin($bin): void
    {
        $this->bin = $bin;
    }
}
