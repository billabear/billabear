<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
    public function convertToDailyOutput(\DateTime $start, \DateTime $end, array $stats, bool $useLastCount = false): array
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
            $lastCount = null;
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    if ($useLastCount && isset($lastCount)) {
                        $output[$brand][$date] = $lastCount;
                    } else {
                        $output[$brand][$date] = 0;
                    }
                } else {
                    $lastCount = $output[$brand][$date];
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
    public function convertToMonthOutput(\DateTime $start, \DateTime $end, array $stats, bool $useLastCount = false): array
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
            $lastCount = null;
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    if ($useLastCount && isset($lastCount)) {
                        $output[$brand][$date] = $lastCount;
                    } else {
                        $output[$brand][$date] = 0;
                    }
                } else {
                    $lastCount = $output[$brand][$date];
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
    public function convertToYearOutput(\DateTime $start, \DateTime $end, array $stats, bool $useLastCount = false): array
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
            $lastCount = null;
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    if ($useLastCount && isset($lastCount)) {
                        $output[$brand][$date] = $lastCount;
                    } else {
                        $output[$brand][$date] = 0;
                    }
                } else {
                    $lastCount = $output[$brand][$date];
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
