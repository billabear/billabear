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

    #[SerializedName('entry_name')]
    private VoucherEntryType $entryType;

    #[SerializedName('automatic_event')]
    private ?VoucherEvent $automaticEvent;

    #[SerializedName('value')]
    private ?int $value;

    private ?string $code;

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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): void
    {
        $this->value = $value;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
