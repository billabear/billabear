<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\Places;

use App\Enum\WorkflowType;

class PlacesManager
{
    /**
     * @var PlaceInterface[]
     */
    private $places = [];

    public function addPlace(PlaceInterface $place): void
    {
        $this->places[] = $place;
    }

    public function getPlacesForWorkflow(WorkflowType $type)
    {
        $output = [];

        foreach ($this->places as $place) {
            if ($place->getWorkflow() === $type) {
                $output[] = $place;
            }
        }

        return $output;
    }
}
