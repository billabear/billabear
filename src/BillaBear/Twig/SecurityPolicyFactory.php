<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Twig;

use Twig\Sandbox\SecurityPolicy;
use Twig\Sandbox\SecurityPolicyInterface;

class SecurityPolicyFactory
{
    public function create(): SecurityPolicyInterface
    {
        $tags = ['if', 'verbatim', 'set', 'for'];
        $filters = [
            'abs',
            'batch',
            'capitalize',
            'column',
            'country_name',
            'currency_name',
            'currency_symbol',
            'date',
            'date_modify',
            'default',
            'e',
            'escape',
            'filter',
            'first',
            'format',
            'format_currency',
            'format_date',
            'format_datetime',
            'format_number',
            'format_time',
            'join',
            'json_encode',
            'keys',
            'language_name',
            'last',
            'length',
            'locale_name',
            'lower',
            'map',
            'merge',
            'nl2br',
            'number_format',
            'reduce',
            'replace',
            'reverse',
            'round',
            'slice',
            'sort',
            'split',
            'striptags',
            'title',
            'trim',
            'upper',
            'url_encode',
            'date_modify',
        ];

        $methods = [];
        $properties = [];
        $functions = ['range', 'max', 'min', 'random', 'date', 'cycle'];

        return new SecurityPolicy($tags, $filters, $methods, $properties, $functions);
    }
}
