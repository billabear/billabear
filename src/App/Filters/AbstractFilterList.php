<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Filters;

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
            $matches = [];
            $subKey = null;

            if (preg_match('~^(.*)\[(.*)\]$~isU', $key, $matches)) {
                $key = $matches[1];
                $subKey = $matches[2];
            }

            if ($request->query->has($key)) {
                $value = $request->get($key);

                if (isset($subKey)) {
                    if (!isset($value[$subKey])) {
                        continue;
                    }
                    $value = $value[$subKey];
                } elseif (is_array($value)) {
                    continue;
                }

                if (isset($data['converter']) && is_callable($data['converter'])) {
                    $value = $data['converter']($value);
                }
                $filter = $this->getFilter($data, $value);
                $output[] = $filter;
            }
        }

        return $output;
    }

    public function getFilter(mixed $data, $value): FilterInterface
    {
        /** @var FilterInterface $filter */
        $filter = new $data['filter']();
        $filter->setFieldName($data['field']);
        $filter->setData($value);

        return $filter;
    }
}
