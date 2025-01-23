<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Generic;

use BillaBear\Entity\GenericBackgroundTask;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.background_task.executor')]
interface ExecutorInterface
{
    public function supports(GenericBackgroundTask $backgroundTask): bool;

    public function execute(GenericBackgroundTask $genericBackgroundTask): void;
}
