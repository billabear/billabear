<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Api\Filters;

use Parthenon\Athena\Filters\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilterList
{
    abstract protected function getFilters(): array;

    public function buildFilters(Request $request): array
    {
        $output = [];
        $filterTypes = $this->getFilters();

        foreach ($filterTypes as $key => $data) {
            if ($request->query->has($key)) {
                /** @var FilterInterface $filter */
                $filter = new $data['filter']();
                $filter->setFieldName($data['field']);
                $filter->setData($request->query->get($key));
                $output[] = $filter;
            }
        }

        return $output;
    }
}
