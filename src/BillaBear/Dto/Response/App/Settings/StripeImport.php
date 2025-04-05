<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class StripeImport
{
    public function __construct(
        public string $id,
        public string $state,
        #[SerializedName('last_id')]
        public ?string $lastId,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
        #[SerializedName('updated_at')]
        public \DateTimeInterface $updateAt,
        #[SerializedName('error')]
        public ?string $error,
        public bool $completed,
        public bool $failed)
    {
    }
}
