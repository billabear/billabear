<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Settings;
use Parthenon\Common\Repository\DoctrineRepository;

class SettingsRepository extends DoctrineRepository implements SettingsRepositoryInterface
{
    public function getDefaultSettings(): Settings
    {
        return $this->entityRepository->findOneBy(['tag' => Settings::DEFAULT_TAG]);
    }
}
