<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Settings;

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
