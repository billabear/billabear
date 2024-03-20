<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats\Graphs;

use App\Entity\Customer;
use App\Entity\Stats\AbstractMoneyStat;

class MoneyStatOutputConverter
{
    /**
     * @param \DateTimeInterface  $start
     * @param \DateTimeInterface  $end
     * @param AbstractMoneyStat[] $stats
     */
    public function convertToDailyOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];
        $foundCurrencies = [];

        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractMoneyStat) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }
            if (!in_array($stat->getCurrency(), $foundCurrencies)) {
                $foundCurrencies[] = $stat->getCurrency();
            }

            $date = $stat->getDate()->format('Y-m-d');
            if (!isset($output[$brand][$date])) {
                $output[$brand][$date] = [];
            }
            $output[$brand][$date][$stat->getCurrency()] = $stat->getAmount();
        }

        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
            $foundCurrencies = ['EUR'];
        }
        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = [];
                }
                foreach ($foundCurrencies as $currency) {
                    if (!isset($output[$brand][$date][$currency])) {
                        $output[$brand][$date][$currency] = 0;
                    }
                }
                $newStart->modify('+1 day');
            }
        }

        return $this->sortArray($output);
    }

    /**
     * @param \DateTimeInterface  $start
     * @param \DateTimeInterface  $end
     * @param AbstractMoneyStat[] $stats
     */
    public function convertToMonthOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];
        $foundCurrencies = [];

        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractMoneyStat) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }
            $monthDate = $stat->getDate()->modify('first day of this month');

            if (!in_array($stat->getCurrency(), $foundCurrencies)) {
                $foundCurrencies[] = $stat->getCurrency();
            }

            if (!$monthDate) {
                throw new \Exception('Invalid date');
            }
            $date = $monthDate->format('Y-m-d');
            if (!isset($output[$brand][$date])) {
                $output[$brand][$date] = [];
            }
            $output[$brand][$date][$stat->getCurrency()] = $stat->getAmount();
        }
        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
            $foundCurrencies = ['USD'];
        }

        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            $newEnd = clone $end;

            $newStart->modify('first day of this month');
            $newEnd->modify('first day of this month');
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = [];
                }
                foreach ($foundCurrencies as $currency) {
                    if (!isset($output[$brand][$date][$currency])) {
                        $output[$brand][$date][$currency] = 0;
                    }
                }
                $newStart->modify('+1 month');
            }
        }

        return $this->sortArray($output);
    }

    /**
     * @param \DateTimeInterface  $start
     * @param \DateTimeInterface  $end
     * @param AbstractMoneyStat[] $stats
     */
    public function convertToYearOutput(\DateTime $start, \DateTime $end, array $stats): array
    {
        $output = [];
        $foundCurrencies = [];
        foreach ($stats as $stat) {
            if (!$stat instanceof AbstractMoneyStat) {
                throw new \LogicException('Stat is not the correct data type');
            }

            $brand = $stat->getBrandCode();
            if (!isset($output[$brand])) {
                $output[$brand] = [];
            }

            if (!in_array($stat->getCurrency(), $foundCurrencies)) {
                $foundCurrencies[] = $stat->getCurrency();
            }

            $monthDate = $stat->getDate()->modify('first day of january');

            if (!$monthDate) {
                throw new \Exception('Invalid date');
            }
            $date = $monthDate->format('Y-m-d');
            if (!isset($output[$brand][$date])) {
                $output[$brand][$date] = [];
            }
            $output[$brand][$date][$stat->getCurrency()] = $stat->getAmount();
        }
        if (empty($output)) {
            $output = [Customer::DEFAULT_BRAND => []];
            $foundCurrencies = ['USD'];
        }

        foreach ($output as $brand => $data) {
            $newStart = clone $start;
            $newEnd = clone $end;

            $newStart->modify('first day of january');
            $newEnd->modify('first day of january');
            while ($newStart <= $end) {
                $date = $newStart->format('Y-m-d');
                if (!isset($output[$brand][$date])) {
                    $output[$brand][$date] = [];
                }
                foreach ($foundCurrencies as $currency) {
                    if (!isset($output[$brand][$date][$currency])) {
                        $output[$brand][$date][$currency] = 0;
                    }
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
