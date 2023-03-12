<?php

namespace App\Api\Filters;

use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Symfony\Component\HttpFoundation\Request;

class CustomerList extends FilterList
{

    protected function getFilters() : array
    {
        return [
            'email' => [
                'field' => 'billingEmail',
                'filter' => ContainsFilter::class,
            ],
            'country' => [
                'field' => '.billingAddress.country',
                'filter' => ExactChoiceFilter::class,
            ],
            'reference' => [
                'field' => 'customerReference',
                'filter' => ContainsFilter::class,
            ],
            'external_reference' => [
                'field' => 'customerReference',
                'filter' => ContainsFilter::class,
            ],
        ];
    }
}