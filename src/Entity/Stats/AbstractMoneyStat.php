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

namespace App\Entity\Stats;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\MappedSuperclass()]
#[ORM\Index(fields: ['year', 'month', 'day'])]
class AbstractMoneyStat
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'integer')]
    protected int $amount = 0;

    #[ORM\Column(type: 'string')]
    protected string $currency;

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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function increaseAmount(Money $newMoney)
    {
        if (!isset($this->currency)) {
            throw new \Exception('No currency set');
        }

        $money = $this->getCountAsMoney();
        $this->setAmountFromMoney($money->plus($newMoney));
    }

    public function getCountAsMoney(): Money
    {
        return Money::ofMinor($this->amount, $this->currency);
    }

    public function setAmountFromMoney(Money $newCount): void
    {
        $this->amount = $newCount->getMinorAmount()->toInt();
        $this->currency = $newCount->getCurrency()->getCurrencyCode();
    }

    public function getBrandCode(): ?string
    {
        return $this->brandCode;
    }

    public function setBrandCode(?string $brandCode): void
    {
        $this->brandCode = $brandCode;
    }
}
