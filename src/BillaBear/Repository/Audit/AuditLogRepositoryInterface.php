<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Audit;

use BillaBear\Dto\Generic\App\AuditLog;
use BillaBear\Logger\Audit\AuditableInterface;
use Parthenon\Athena\ResultSet;

interface AuditLogRepositoryInterface
{
    /**
     * @return AuditLog[]
     */
    public function findAll(?string $lastId, int $limit = 25, bool $reverse = false): ResultSet;

    /**
     * @return AuditLog[]
     */
    public function findAllForAuditableEntity(AuditableInterface $auditable, ?string $lastId, int $limit = 25, bool $reverse = false): ResultSet;
}
