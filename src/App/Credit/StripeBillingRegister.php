<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
