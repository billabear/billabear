<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Filters;

use Parthenon\Athena\Filters\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilterList
{
    public function __construct(private array $data = [])
    {
    }

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

            if (null !== $value = $this->getValue($request, $key)) {
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

    abstract protected function getFilters(): array;

    private function getValue(Request $request, string $key): mixed
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        if ($request->query->has($key)) {
            return $request->get($key);
        }

        return null;
    }
}
