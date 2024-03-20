<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App;

use App\Enum\VoucherEntryType;
use App\Enum\VoucherEvent;
use App\Enum\VoucherType;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Voucher
{
    private $id;

    #[SerializedName('name')]
    private string $name;

    #[SerializedName('type')]
    private VoucherType $type;

    #[SerializedName('entry_type')]
    private VoucherEntryType $entryType;

    #[SerializedName('automatic_event')]
    private ?VoucherEvent $automaticEvent;

    #[SerializedName('percentage')]
    private ?int $percentage;

    private ?string $code;

    private bool $disabled;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): VoucherType
    {
        return $this->type;
    }

    public function setType(VoucherType $type): void
    {
        $this->type = $type;
    }

    public function getEntryType(): VoucherEntryType
    {
        return $this->entryType;
    }

    public function setEntryType(VoucherEntryType $entryType): void
    {
        $this->entryType = $entryType;
    }

    public function getAutomaticEvent(): ?VoucherEvent
    {
        return $this->automaticEvent;
    }

    public function setAutomaticEvent(?VoucherEvent $automaticEvent): void
    {
        $this->automaticEvent = $automaticEvent;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(?int $percentage): void
    {
        $this->percentage = $percentage;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
