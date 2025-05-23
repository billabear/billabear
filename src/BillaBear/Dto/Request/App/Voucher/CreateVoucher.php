<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Voucher;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateVoucher
{
    #[Assert\Choice(choices: ['percentage', 'fixed_credit'])]
    #[Assert\NotBlank]
    private $type;

    #[Assert\Choice(choices: ['manual', 'automatic'])]
    #[Assert\NotBlank]
    #[SerializedName('entry_type')]
    private $entryType;

    #[Assert\Choice(choices: ['expired_card_added'])]
    #[SerializedName('entry_event')]
    private $entryEvent;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    private $name;

    #[Assert\LessThanOrEqual(100)]
    #[Assert\Positive]
    private $percentage;

    #[Assert\Valid]
    private array $amounts = [];

    #[Assert\Type('string')]
    private $code;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getEntryType()
    {
        return $this->entryType;
    }

    public function setEntryType($entryType): void
    {
        $this->entryType = $entryType;
    }

    public function getEntryEvent()
    {
        return $this->entryEvent;
    }

    public function setEntryEvent($entryEvent): void
    {
        $this->entryEvent = $entryEvent;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

    public function setPercentage($percentage): void
    {
        $this->percentage = $percentage;
    }

    public function addAmount(CreateVoucherAmount $amount): void
    {
        $this->amounts[] = $amount;
    }

    /**
     * @return CreateVoucherAmount[]
     */
    public function getAmounts(): array
    {
        return $this->amounts;
    }

    public function setAmounts(array $amounts): void
    {
        $this->amounts = $amounts;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ('fixed_credit' === $this->type) {
            if (empty($this->amounts)) {
                $context->buildViolation('Need amounts when type is fixed credit')->atPath('amounts')->addViolation();
            }
        } else {
            if (empty($this->percentage)) {
                $context->buildViolation('Percentage required when type is percentage')->atPath('percentage')->addViolation();
            }
        }

        if ('manual' === $this->entryType) {
            if (empty($this->code)) {
                $context->buildViolation('Need code when entry type is manual')->atPath('code')->addViolation();
            }
        }
    }
}
