<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SystemSettings
{
    #[SerializedName('system_url')]
    private ?string $systemUrl = null;

    #[SerializedName('timezone')]
    private ?string $timezone = null;

    public function getSystemUrl(): ?string
    {
        return $this->systemUrl;
    }

    public function setSystemUrl(?string $systemUrl): void
    {
        $this->systemUrl = $systemUrl;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }
}
