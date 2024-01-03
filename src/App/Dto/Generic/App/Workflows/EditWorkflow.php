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

namespace App\Dto\Generic\App\Workflows;

use Symfony\Component\Serializer\Attribute\SerializedName;

class EditWorkflow
{
    #[SerializedName('handlers')]
    private array $transitionHandlers = [];

    private array $places = [];

    public function getTransitionHandlers(): array
    {
        return $this->transitionHandlers;
    }

    public function setTransitionHandlers(array $transitionHandlers): void
    {
        $this->transitionHandlers = $transitionHandlers;
    }

    public function getPlaces(): array
    {
        return $this->places;
    }

    public function setPlaces(array $places): void
    {
        $this->places = $places;
    }
}
