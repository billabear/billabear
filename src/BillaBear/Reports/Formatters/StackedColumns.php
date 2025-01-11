<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Reports\Formatters;

class StackedColumns
{
    public function formatMonthly(array $data): array
    {
        $arr = [];

        $xAxis = [];

        foreach ($data as $row) {
            $key = '01/'.$row['month'].'/'.$row['year'];
            if (!in_array($key, $xAxis)) {
                $xAxis[] = $key;
            }
            if (!isset($arr[$row['name']])) {
                $arr[$row['name']] = [];
            }

            $arr[$row['name']][] = $row['amount'];
        }

        $output = ['series' => [], 'xaxis' => $xAxis];
        foreach ($arr as $key => $values) {
            $output['series'][] = [
                'name' => $key,
                'data' => $values,
            ];
        }

        return $output;
    }
}
