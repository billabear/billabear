<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity\Stats;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\MappedSuperclass()]
class AbstractStats
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'integer')]
    protected int $count = 0;

    #[ORM\Column(type: 'integer')]
    protected int $year;

    #[ORM\Column(type: 'integer')]
    protected int $month;

    #[ORM\Column(type: 'integer')]
    protected int $day;

    #[ORM\Column(type: 'date')]
    protected \DateTime $date;

    #[ORM\Column(type: 'string')]
    protected string $brandCode;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
        $this->setDatetime();
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): void
    {
        $this->month = $month;
        $this->setDatetime();
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function setDay(int $day): void
    {
        $this->day = $day;
        $this->setDatetime();
    }

    public function increaseCount()
    {
        if (!isset($this->count)) {
            $this->count = 1;

            return;
        }
        ++$this->count;
    }

    public function getBrandCode(): string
    {
        return $this->brandCode;
    }

    public function setBrandCode(string $brandCode): void
    {
        $this->brandCode = $brandCode;
    }

    public function getDateAsDateTime(): \DateTime
    {
        return new \DateTime(sprintf('%d-%d-%d', $this->year, $this->month, $this->day));
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    protected function setDatetime(): void
    {
        if (!isset($this->year)) {
            return;
        }

        if (!isset($this->month)) {
            return;
        }

        if (!isset($this->day)) {
            return;
        }

        $this->date = new \DateTime(sprintf('%d-%d-%d', $this->year, $this->month, $this->day));
    }
}
