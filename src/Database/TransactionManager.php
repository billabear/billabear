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

namespace App\Database;

use Doctrine\ORM\EntityManagerInterface;

class TransactionManager
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function start(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function finish(): void
    {
        $this->entityManager->commit();
    }

    public function abort(): void
    {
        $this->entityManager->rollback();
    }

    public function wrapCallable(callable $callback): void
    {
        $this->entityManager->wrapInTransaction($callback);
    }
}
