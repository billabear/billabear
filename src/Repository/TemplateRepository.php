<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\Template;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class TemplateRepository extends DoctrineRepository implements TemplateRepositoryInterface
{
    public function getByGroup(string $group): array
    {
        return $this->entityRepository->findBy(['group' => $group]);
    }

    public function getByNameAndGroup(string $name, string $group): Template
    {
        $template = $this->entityRepository->findOneBy(['name' => $name, 'group' => $group]);

        if (!$template instanceof Template) {
            throw new NoEntityFoundException(sprintf("Can't find a template for name '%s' and group '%s'", $name, $group));
        }

        return $template;
    }
}
