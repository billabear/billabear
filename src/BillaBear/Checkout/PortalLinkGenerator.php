<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Checkout;

use BillaBear\Entity\Checkout;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class PortalLinkGenerator implements PortalLinkGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function generatePayLink(Checkout $checkout): string
    {
        $payLink = $this->urlGenerator->generate('portal_pay_checkout', ['slug' => $checkout->getSlug()]);

        return $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl().$payLink;
    }
}
