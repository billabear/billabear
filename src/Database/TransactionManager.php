<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
