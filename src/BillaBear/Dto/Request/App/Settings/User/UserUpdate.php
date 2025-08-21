<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings\User;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserUpdate
{
    public function __construct(
        #[Assert\NotBlank]
        public array $roles = [],
        #[Assert\Email]
        #[Assert\NotBlank]
        public ?string $email = null,
    ) {
    }
}
