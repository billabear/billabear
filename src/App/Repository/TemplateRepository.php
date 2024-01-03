<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
