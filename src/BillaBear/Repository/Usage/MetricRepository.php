<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\Usage\Metric;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class MetricRepository extends DoctrineCrudRepository implements MetricRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getByCode(string $code): Metric
    {
        $metric = $this->entityRepository->findOneBy(['code' => $code]);

        if (!$metric instanceof Metric) {
            throw new NoEntityFoundException(sprintf("Can't find metric for code '%s'", $code));
        }

        return $metric;
    }
}
