<?php

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Api\Filters;

use Parthenon\Athena\Filters\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class FilterList
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
