<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\CountryTaxRule;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
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

            if ($countryTaxRule->getId() == $openEndedTaxRule->getId()) {
                return;
            }

            $date = clone $countryTaxRule->getValidFrom();
            if (null !== $countryTaxRule->getValidUntil() && $countryTaxRule->getValidUntil() < $openEndedTaxRule->getValidFrom()) {
                $this->getLogger()->info('New country tax rule expires before the current open ended rule');

                return;
            }

            $date->modify('-1 minute');
            $openEndedTaxRule->setValidUntil($date);
            $this->countryTaxRuleRepository->save($openEndedTaxRule);
        } catch (NoEntityFoundException $exception) {
            $this->getLogger()->info('No open ended country tax rule found');

            return;
        }
    }
}
