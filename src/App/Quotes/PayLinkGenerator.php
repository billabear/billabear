<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Quotes;

use App\Entity\Quote;
use App\Repository\SettingsRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayLinkGenerator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function generatePayLink(Quote $quote): string
    {
        $payLink = $this->urlGenerator->generate('portal_pay_quote', ['hash' => $quote->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        $fullPayLink = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl().$payLink;

        return $fullPayLink;
    }
}
