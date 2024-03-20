<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Generic;

use App\Entity\GenericBackgroundTask;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.background_task.executor')]
interface ExecutorInterface
{
    public function supports(GenericBackgroundTask $backgroundTask): bool;

    public function execute(GenericBackgroundTask $genericBackgroundTask): void;
}
