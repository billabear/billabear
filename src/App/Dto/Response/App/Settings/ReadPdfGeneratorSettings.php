<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ReadPdfGeneratorSettings
{
    private string $generator;

    #[SerializedName('tmp_dir')]
    private ?string $tmpDir;

    #[SerializedName('api_key')]
    private ?string $apiKey;

    private ?string $bin;

    public function getGenerator(): string
    {
        return $this->generator;
    }

    public function setGenerator(string $generator): void
    {
        $this->generator = $generator;
    }

    public function getTmpDir(): ?string
    {
        return $this->tmpDir;
    }

    public function setTmpDir(?string $tmpDir): void
    {
        $this->tmpDir = $tmpDir;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getBin(): ?string
    {
        return $this->bin;
    }

    public function setBin(?string $bin): void
    {
        $this->bin = $bin;
    }
}
