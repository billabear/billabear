<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\SlackWebhook;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class SlackWebhookRepository extends DoctrineCrudRepository implements SlackWebhookRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getByName(string $name): SlackWebhook
    {
        $webhook = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$webhook) {
            throw new NoEntityFoundException(sprintf("Can't find a webhook for %s", $name));
        }

        return $webhook;
    }

    public function getAllEnable(): array
    {
        return $this->entityRepository->findBy(['enabled' => true]);
    }
}
