<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
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
