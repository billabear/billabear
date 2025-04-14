<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Entity\ManageCustomerSession;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PortalManageLinkGenerator implements PortalManageLinkGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function generate(ManageCustomerSession $session): string
    {
        $payLink = $this->urlGenerator->generate('portal_customer_manage', ['token' => $session->getToken()], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl().$payLink;
    }
}
