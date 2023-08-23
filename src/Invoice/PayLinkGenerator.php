<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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