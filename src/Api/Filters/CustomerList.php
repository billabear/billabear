<?php

namespace App\Api\Filters;

use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;

class CustomerList extends FilterList
{
    protected function getFilters(): array
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
                'field' => 'reference',
                'filter' => ContainsFilter::class,
            ],
            'external_reference' => [
                'field' => 'externalCustomerReference',
                'filter' => ContainsFilter::class,
            ],
        ];
    }
}
