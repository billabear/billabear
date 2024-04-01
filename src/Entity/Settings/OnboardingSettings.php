<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class OnboardingSettings
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $hasStripeImports = false;

    public function isHasStripeImports(): bool
    {
        return true === $this->hasStripeImports;
    }

    public function setHasStripeImports(bool $hasStripeImports): void
    {
        $this->hasStripeImports = $hasStripeImports;
    }
}
