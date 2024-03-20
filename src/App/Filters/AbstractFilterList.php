<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
