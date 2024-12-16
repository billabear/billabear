<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api;

use BillaBear\Dto\Request\Api\Price\CreateTier;
use BillaBear\Pricing\Usage\MetricType;
use BillaBear\Validator\Constraints\MetricExists;
use Parthenon\Billing\Enum\PriceType;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreatePrice
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    #[SerializedName('amount')]
    private $amount;

    #[Assert\NotBlank]
    #[Assert\Currency]
    #[SerializedName('currency')]
    private $currency;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('external_reference')]
    private $external_reference;

    #[Assert\Type(type: 'boolean')]
    #[SerializedName('recurring')]
    private $recurring;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Choice(['week', 'month', 'year'])]
    #[SerializedName('schedule')]
    private $schedule;

    #[SerializedName('including_tax')]
    #[Assert\Type(type: 'boolean')]
    private $including_tax;

    #[SerializedName('public')]
    private $public = true;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Choice(choices: [PriceType::ONE_OFF->value, PriceType::FIXED_PRICE->value, PriceType::PACKAGE->value, PriceType::TIERED_GRADUATED->value, PriceType::TIERED_VOLUME->value, PriceType::UNIT->value])]
    private $type;

    #[Assert\When(
        expression: 'this.getUsage() == true',
        constraints: [
            new Assert\NotBlank(),
            new Assert\Choice(choices: MetricType::TYPES),
        ],
    )]
    private $metric_type;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    private $units;

    #[Assert\Type(type: 'boolean')]
    private $usage;

    #[Assert\Valid]
    private array $tiers = [];

    #[Assert\When(
        expression: 'this.getUsage() == true',
        constraints: [
            new Assert\NotBlank(),
            new MetricExists(),
        ],
    )]
    private $metric;

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
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

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getExternalReference(): ?string
    {
        return $this->external_reference;
    }

    public function hasExternalReference(): bool
    {
        return isset($this->external_reference);
    }

    public function setExternalReference(?string $external_reference): void
    {
        $this->external_reference = $external_reference;
    }

    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function setIncludingTax(bool $including_tax): void
    {
        $this->including_tax = $including_tax;
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function isIncludingTax(): bool
    {
        return true === $this->including_tax;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function setUnits($units): void
    {
        $this->units = $units;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getUsage()
    {
        return true === $this->usage;
    }

    public function setUsage($usage): void
    {
        $this->usage = $usage;
    }

    public function addTier(CreateTier $createTier)
    {
        $this->tiers[] = $createTier;
    }

    public function getTiers(): array
    {
        return $this->tiers;
    }

    public function setTiers(array $tiers): void
    {
        $this->tiers = $tiers;
    }

    public function getMetric()
    {
        return $this->metric;
    }

    public function setMetric($metric): void
    {
        $this->metric = $metric;
    }

    public function getMetricType()
    {
        return $this->metric_type;
    }

    public function setMetricType($metric_type): void
    {
        $this->metric_type = $metric_type;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if (!isset($this->amount) && ($this->type !== PriceType::TIERED_VOLUME->value && $this->type !== PriceType::TIERED_GRADUATED->value)) {
            $context->buildViolation('Amount must be provided if type is not tiered')
                ->atPath('amount')
                ->addViolation();
        }

        if (!isset($this->units) && $this->type === PriceType::PACKAGE->value) {
            $context->buildViolation('Units must be provided if type is package')
                ->atPath('units')
                ->addViolation();
        }

        $lastUnit = 0;
        if (count($this->tiers) > 0) {
            /* @var CreateTier[] $tiers */
            usort($this->tiers, function (CreateTier $a, CreateTier $b) {
                return $a->getFirstUnit() <=> $b->getFirstUnit();
            });
            /** @var CreateTier $tier */
            foreach ($this->tiers as $tier) {
                if ($tier->getFirstUnit() <= $lastUnit) {
                    $context->buildViolation('Tiers contain invalid first and last unit configuration')
                        ->atPath('tiers')
                        ->addViolation();
                }

                $expectedUnit = $lastUnit + 1;
                if ($expectedUnit !== $tier->getFirstUnit()) {
                    $context->buildViolation("Tiers don't align correctly")
                        ->atPath('tiers')
                        ->addViolation();
                }

                $lastUnit = $tier->getLastUnit();
            }
        }
    }
}
