<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Stats;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Index(fields: ['year', 'month', 'day'])]
#[ORM\MappedSuperclass]
class AbstractMoneyStat
{
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
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

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
