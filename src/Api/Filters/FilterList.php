<?php

namespace App\Api\Filters;

use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Parthenon\Athena\Filters\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class FilterList
{
    abstract protected function getFilters() : array;

    public function buildFilters(Request $request) : array {
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