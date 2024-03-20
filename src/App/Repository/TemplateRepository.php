<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\Template;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class TemplateRepository extends DoctrineRepository implements TemplateRepositoryInterface
{
    public function getByBrand(string $brand): array
    {
        return $this->entityRepository->findBy(['brand' => $brand]);
    }

    public function getByNameAndBrand(string $name, string $brand): Template
    {
        $template = $this->entityRepository->findOneBy(['name' => $name, 'brand' => $brand]);

        if (!$template instanceof Template) {
            throw new NoEntityFoundException(sprintf("Can't find a template for name '%s' and brand '%s'", $name, $brand));
        }

        return $template;
    }
}
