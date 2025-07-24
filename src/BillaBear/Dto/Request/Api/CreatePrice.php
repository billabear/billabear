<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api;

use BillaBear\Dto\Request\Api\Price\CreateTier;
use BillaBear\Pricing\Usage\MetricType;
use BillaBear\Validator\Constraints\MetricExists;
use Parthenon\Billing\Enum\PriceType;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

readonly class CreatePrice
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Positive]
        #[Assert\Type(type: 'integer')]
        #[SerializedName('amount')]
        public ?int $amount = null,

        #[Assert\Currency]
        #[Assert\NotBlank]
        #[SerializedName('currency')]
        public ?string $currency = null,

        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('external_reference')]
        public ?string $external_reference = null,

        #[Assert\Type(type: 'boolean')]
        #[SerializedName('recurring')]
        public ?bool $recurring = null,

        #[Assert\Choice(['week', 'month', 'year'])]
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('schedule')]
        public ?string $schedule = null,

        #[Assert\Type(type: 'boolean')]
        #[SerializedName('including_tax')]
        public ?bool $including_tax = null,

        #[SerializedName('public')]
        public bool $public = true,

        #[Assert\Choice(choices: [PriceType::ONE_OFF->value, PriceType::FIXED_PRICE->value, PriceType::PACKAGE->value, PriceType::TIERED_GRADUATED->value, PriceType::TIERED_VOLUME->value, PriceType::UNIT->value])]
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type(type: 'string')]
        public ?string $type = null,

        #[Assert\When(
            expression: 'this.usage == true',
            constraints: [
                new Assert\NotBlank(),
                new Assert\Choice(choices: MetricType::TYPES),
            ],
        )]
        public ?string $metric_type = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Positive]
        #[Assert\Type(type: 'integer')]
        public ?int $units = null,

        #[Assert\Type(type: 'boolean')]
        public ?bool $usage = null,

        #[Assert\Valid]
        public array $tiers = [],

        #[Assert\When(
            expression: 'this.usage == true',
            constraints: [
                new Assert\NotBlank(),
                new MetricExists(),
            ],
        )]
        public ?string $metric = null,
    ) {
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
                return $a->first_unit <=> $b->first_unit;
            });
            /** @var CreateTier $tier */
            foreach ($this->tiers as $tier) {
                if ($tier->first_unit <= $lastUnit) {
                    $context->buildViolation('Tiers contain invalid first and last unit configuration')
                        ->atPath('tiers')
                        ->addViolation();
                }

                $expectedUnit = $lastUnit + 1;
                if ($expectedUnit !== $tier->first_unit) {
                    $context->buildViolation("Tiers don't align correctly")
                        ->atPath('tiers')
                        ->addViolation();
                }

                $lastUnit = $tier->last_unit;
            }
        }
    }
}
