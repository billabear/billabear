<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
