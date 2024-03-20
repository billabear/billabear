<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\Places;

use App\Enum\WorkflowType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.workflow.place')]
interface PlaceInterface
{
    public function getName(): string;

    public function getPriority(): int;

    public function getWorkflow(): WorkflowType;

    public function getToTransitionName(): string;
}
