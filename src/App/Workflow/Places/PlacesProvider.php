<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\Places;

use App\Enum\WorkflowType;
use App\Repository\WorkflowTransitionRepositoryInterface;

class PlacesProvider
{
    /**
     * @var PlaceInterface[]
     */
    private $places = [];

    public function __construct(private WorkflowTransitionRepositoryInterface $workflowTransitionRepository)
    {
    }

    public function addPlace(PlaceInterface $place): void
    {
        $this->places[] = $place;
    }

    /**
     * @return PlaceInterface[]
     */
    public function getPlacesForWorkflow(WorkflowType $type): array
    {
        $output = $this->workflowTransitionRepository->findForWorkflow($type);

        foreach ($this->places as $place) {
            if ($place->getWorkflow() === $type) {
                $output[] = $place;
            }
        }

        usort($output, function (PlaceInterface $a, PlaceInterface $b) {
            return $a->getPriority() <=> $b->getPriority();
        });

        return $output;
    }
}
