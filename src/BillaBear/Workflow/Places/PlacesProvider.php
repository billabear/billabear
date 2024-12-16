<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Places;

use BillaBear\Repository\WorkflowTransitionRepositoryInterface;
use BillaBear\Workflow\WorkflowType;

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

    /**
     * @return PlaceInterface[]
     */
    public function getEnabledPlacesForWorkflow(WorkflowType $type): array
    {
        $output = $this->workflowTransitionRepository->findEnabledForWorkflow($type);

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
