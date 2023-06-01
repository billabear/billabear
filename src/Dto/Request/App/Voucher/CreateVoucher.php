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

namespace App\Dto\Request\App\Voucher;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateVoucher
{
    #[Assert\NotBlank()]
    #[Assert\Choice(choices: ['percentage', 'fixed_credit'])]
    private $type;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['manual', 'automatic'])]
    #[SerializedName('entry_type')]
    private $entryType;

    #[Assert\Choice(choices: ['expired_card_added'])]
    #[SerializedName('entry_event')]
    private $entryEvent;

    #[Assert\Type(type: 'string')]
    private $name;

    #[Assert\Positive()]
    private $value;

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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }
}
