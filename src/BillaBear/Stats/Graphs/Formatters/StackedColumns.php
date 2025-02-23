<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats\Graphs\Formatters;

class StackedColumns
{
    public function formatDaily(array $data): array
    {
        $arr = [];

        $xAxis = [];

        foreach ($data as $row) {
            $key = $row['day'].'/'.$row['month'].'/'.$row['year'];
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

    public function formatYearly(array $data): array
    {
        $arr = [];

        $xAxis = [];

        foreach ($data as $row) {
            $key = '01/01/'.$row['year'];
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
