<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\BrandSettings;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class BrandSettingsRepository extends DoctrineRepository implements BrandSettingsRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getByCode(string $code): BrandSettings
    {
        $brandSettings = $this->entityRepository->findOneBy(['code' => $code]);

        if (!$brandSettings instanceof BrandSettings) {
            throw new NoEntityFoundException(sprintf("Can't find brand settings for code '%s'", $code));
        }

        return $brandSettings;
    }
}
