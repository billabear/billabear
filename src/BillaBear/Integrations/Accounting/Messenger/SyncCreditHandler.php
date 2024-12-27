<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Repository\CreditRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncCreditHandler
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
        private \BillaBear\Integrations\Accounting\Action\SyncCredit $syncCredit,
    ) {
    }

    public function __invoke(SyncCredit $syncCredit): void
    {
        $credit = $this->creditRepository->findById($syncCredit->creditId);
        $this->syncCredit->sync($credit);
    }
}
