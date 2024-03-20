<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice;

use App\Entity\Invoice;
use App\Repository\SettingsRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayLinkGenerator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function generatePayLink(Invoice $invoice): string
    {
        $payLink = $this->urlGenerator->generate('portal_pay_invoice', ['hash' => $invoice->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        $fullPayLink = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl().$payLink;

        return $fullPayLink;
    }
}
