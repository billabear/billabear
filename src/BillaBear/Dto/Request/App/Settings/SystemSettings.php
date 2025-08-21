<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SystemSettings
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        #[SerializedName('system_url')]
        public string $systemUrl,
        #[Assert\NotBlank]
        #[Assert\Timezone]
        public string $timezone,
        public ?string $invoiceNumberGeneration = null,
        public ?int $subsequentialNumber = null,
        public ?string $defaultInvoiceDueTime = null,
        public ?string $format = null,
    ) {
    }
}
