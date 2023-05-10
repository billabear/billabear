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

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\MappedSuperclass()]
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
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): void
    {
        $this->month = $month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function setDay(int $day): void
    {
        $this->day = $day;
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
}
