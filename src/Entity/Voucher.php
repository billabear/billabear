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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?int $percentage = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $code;

    #[ORM\Column(type: 'string', enumType: VoucherEvent::class, nullable: true)]
    private ?VoucherEvent $entryEvent = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?BillingAdminInterface $billingAdmin = null;

    #[ORM\OneToMany(targetEntity: VoucherAmount::class, mappedBy: 'voucher', cascade: ['persist', 'remove'])]
    private Collection $amounts;

    #[Orm\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $disabled = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $externalReference = null;

    public function __construct()
    {
        $this->amounts = new ArrayCollection();
    }

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

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(?int $percentage): void
    {
        $this->percentage = $percentage;
    }

    public function getEntryEvent(): ?VoucherEvent
    {
        return $this->entryEvent;
    }

    public function setEntryEvent(?VoucherEvent $entryEvent): void
    {
        $this->entryEvent = $entryEvent;
    }

    public function getBillingAdmin(): ?BillingAdminInterface
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?BillingAdminInterface $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    /**
     * @return Collection|VoucherAmount[]
     */
    public function getAmounts(): Collection
    {
        return $this->amounts;
    }

    public function setAmounts(Collection $amounts): void
    {
        $this->amounts = $amounts;
    }

    public function addAmountVoucher(VoucherAmount $amount): void
    {
        $amount->setVoucher($this);
        $this->amounts->add($amount);
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
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

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }
}
