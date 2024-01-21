<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tax;

use App\Entity\CountryTaxRule;
use App\Repository\CountryTaxRuleRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class CountryTaxRuleTerminator
{
    use LoggerAwareTrait;

    public function __construct(private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository)
    {
    }

    public function terminateOpenTaxRule(CountryTaxRule $countryTaxRule): void
    {
        try {
            $openEndedTaxRule = $this->countryTaxRuleRepository->getOpenEndedForCountryAndTaxType($countryTaxRule->getCountry(), $countryTaxRule->getTaxType());
            $date = clone $countryTaxRule->getValidFrom();
            $date->modify('-1 minute');
            $openEndedTaxRule->setValidUntil($date);
            $this->countryTaxRuleRepository->save($openEndedTaxRule);
        } catch (NoEntityFoundException $exception) {
            $this->getLogger()->info('No open ended country tax rule found');

            return;
        }
    }
}
