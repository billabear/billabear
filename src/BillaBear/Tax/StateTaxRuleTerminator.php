<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\StateTaxRule;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class StateTaxRuleTerminator
{
    use LoggerAwareTrait;

    public function __construct(private StateTaxRuleRepositoryInterface $stateTaxRuleRepository)
    {
    }

    public function terminateOpenTaxRule(StateTaxRule $stateTaxRule): void
    {
        try {
            $openEndedTaxRule = $this->stateTaxRuleRepository->getOpenEndedForCountryStateAndTaxType($stateTaxRule->getState(), $stateTaxRule->getTaxType());

            if ($stateTaxRule->getId() == $openEndedTaxRule->getId()) {
                return;
            }

            $date = clone $stateTaxRule->getValidFrom();
            if (null !== $stateTaxRule->getValidUntil() && $stateTaxRule->getValidUntil() < $openEndedTaxRule->getValidFrom()) {
                $this->getLogger()->info('New state tax rule expires before the current open ended rule');

                return;
            }

            $date->modify('-1 minute');
            $openEndedTaxRule->setValidUntil($date);
            $this->stateTaxRuleRepository->save($openEndedTaxRule);
        } catch (NoEntityFoundException $exception) {
            $this->getLogger()->info('No open ended state tax rule found');

            return;
        }
    }
}
