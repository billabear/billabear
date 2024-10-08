<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Entity\Usage\Metric;
use BillaBear\Enum\MetricType;
use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\Product;

/**
 * @method Product getProduct()
 */
#[ORM\Entity]
#[ORM\Table('price')]
class Price extends \Parthenon\Billing\Entity\Price
{
    #[ORM\ManyToOne(targetEntity: Metric::class, cascade: ['PERSIST'])]
    private ?Metric $metric = null;

    #[ORM\Column(type: 'string', enumType: MetricType::class, nullable: true)]
    private ?MetricType $metricType = null;

    public function isSameSchedule(Price $price): bool
    {
        if ($this->getSchedule() === $price->getSchedule()) {
            return true;
        }

        return false;
    }

    public function getMetric(): ?Metric
    {
        return $this->metric;
    }

    public function setMetric(?Metric $metric): void
    {
        $this->metric = $metric;
    }

    public function getMetricType(): ?MetricType
    {
        return $this->metricType;
    }

    public function setMetricType(?MetricType $metricType): void
    {
        $this->metricType = $metricType;
    }

    public function getDisplayName(): string
    {
        $output = $this->type->value.' - ';
        if ($this->amount) {
            $output .= (string) $this->getAsMoney();
        }

        if (!empty($this->tierComponents)) {
            /** @var TierComponent $tierComponent */
            foreach ($this->getTierComponents() as $tierComponent) {
                $unitPriceMoney = Money::ofMinor($tierComponent->getUnitPrice(), $this->getCurrency());
                $flatFeeMoney = Money::ofMinor($tierComponent->getFlatFee(), $this->getCurrency());
                $output .= sprintf(
                    '[%s/%s]',
                    (string) $unitPriceMoney,
                    (string) $flatFeeMoney);
                break;
            }
            $output = rtrim($output, '/').' - ';
        }

        if ($this->recurring) {
            $output .= $this->schedule;
        } else {
            $output .= 'one-off';
        }

        return $output;
    }
}
