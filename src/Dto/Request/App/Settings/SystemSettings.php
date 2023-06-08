<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class SystemSettings
{
    #[Assert\NotBlank]
    #[Assert\Url]
    #[SerializedName('system_url')]
    private $systemUrl;

    #[Assert\NotBlank]
    #[Assert\Timezone]
    private $timezone;

    public function getSystemUrl()
    {
        return $this->systemUrl;
    }

    public function setSystemUrl($systemUrl): void
    {
        $this->systemUrl = $systemUrl;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }
}
