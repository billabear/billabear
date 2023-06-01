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

namespace App\Entity;

use App\Enum\VoucherEntryType;
use App\Enum\VoucherEvent;
use App\Enum\VoucherType;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'vouchers')]
class Voucher
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', enumType: VoucherType::class)]
    private VoucherType $type;

    #[ORM\Column(type: 'string', enumType: VoucherEntryType::class)]
    private VoucherEntryType $entryType;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $value = null;

    #[ORM\Column(type: 'string', enumType: VoucherEvent::class, nullable: true)]
    private ?VoucherEvent $automaticEvent = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?BillingAdminInterface $billingAdmin = null;

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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): void
    {
        $this->value = $value;
    }

    public function getAutomaticEvent(): ?VoucherEvent
    {
        return $this->automaticEvent;
    }

    public function setAutomaticEvent(?VoucherEvent $automaticEvent): void
    {
        $this->automaticEvent = $automaticEvent;
    }

    public function getBillingAdmin(): ?BillingAdminInterface
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?BillingAdminInterface $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }
}
