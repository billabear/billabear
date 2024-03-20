<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\BrandSettings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class BrandSettingsView
{
    #[SerializedName('brand')]
    private BrandSettings $brandSettings;

    public function getBrandSettings(): BrandSettings
    {
        return $this->brandSettings;
    }

    public function setBrandSettings(BrandSettings $brandSettings): void
    {
        $this->brandSettings = $brandSettings;
    }
}
