<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\StripeImport;
use Parthenon\Common\Repository\RepositoryInterface;

interface StripeImportRepositoryInterface extends RepositoryInterface
{
    /**
     * @return StripeImport[]
     */
    public function getAll(): array;

    public function findActive(): ?StripeImport;
}
