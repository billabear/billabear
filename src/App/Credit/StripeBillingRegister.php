<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Credit;

use App\Entity\Credit;
use App\Settings\StripeBillingTrait;
use Obol\Model\Credit\CreditTransaction;
use Obol\Provider\ProviderInterface;
use Parthenon\Common\LoggerAwareTrait;

class StripeBillingRegister
{
    use StripeBillingTrait;
    use LoggerAwareTrait;

    public function __construct(private ProviderInterface $provider)
    {
    }

    public function register(Credit $credit): void
    {
        if (!$this->isStripeBillingEnabled()) {
            return;
        }
        $this->getLogger()->info('Registering credit adjustment with Stripe Billing');

        $creditTransaction = new CreditTransaction();
        $creditTransaction->setType($credit->getType());
        $creditTransaction->setAmount($credit->getAmount());
        $creditTransaction->setCurrency($credit->getCurrency());
        $creditTransaction->setCustomerReference($credit->getCustomer()->getExternalCustomerReference());
        $this->provider->credit()->addCreditTransaction($creditTransaction);
    }
}
