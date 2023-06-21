<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Stats\Graphs;

use App\Entity\Customer;
use App\Entity\Stats\AbstractStats;

class StatOutputConverter
{
    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param AbstractStats[]    $stats
     */
    public function convertToDailyOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];

        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractStats) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }

            $date = $stat->getDateAsDateTime()->format('Y-m-d');
            $output[$brand][$date] = $stat->getCount();
        }
        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
        }

        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = 0;
                }
                $newStart->modify('+1 day');
            }
        }

        return $this->sortArray($output);
    }

    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param AbstractStats[]    $stats
     */
    public function convertToMonthOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];

        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractStats) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }
            $monthDate = $stat->getDateAsDateTime()->modify('first day of this month');

            if (!$monthDate) {
                throw new \Exception('Invalid date');
            }
            $date = $monthDate->format('Y-m-d');
            $output[$brand][$date] = $stat->getCount();
        }

        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
        }

        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            $newEnd = clone $end;

            $newStart->modify('first day of this month');
            $newEnd->modify('first day of this month');
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = 0;
                }
                $newStart->modify('+1 month');
            }
        }

        return $this->sortArray($output);
    }

    /**
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param AbstractStats[]    $stats
     */
    public function convertToYearOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];

        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractStats) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }
            $monthDate = $stat->getDateAsDateTime()->modify('first day of january');

            if (!$monthDate) {
                throw new \Exception('Invalid date');
            }
            $date = $monthDate->format('Y-m-d');
            $output[$brand][$date] = $stat->getCount();
        }

        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
        }
        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            $newEnd = clone $end;

            $newStart->modify('first day of january');
            $newEnd->modify('first day of january');
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = 0;
                }
                $newStart->modify('+1 year');
            }
        }

        return $this->sortArray($output);
    }

    private function sortArray($input)
    {
        foreach (array_keys($input) as $key) {
            ksort($input[$key]);
        }

        return $input;
    }
}
