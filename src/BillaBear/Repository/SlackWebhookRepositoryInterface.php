<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\SlackWebhook;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface SlackWebhookRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return SlackWebhook[]
     */
    public function getAll(): array;

    /**
     * @return SlackWebhook[]
     */
    public function getAllEnable(): array;

    public function getByName(string $name): SlackWebhook;
}
