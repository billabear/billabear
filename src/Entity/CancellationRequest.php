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

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Subscription;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'cancellation_requests')]
class CancellationRequest
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column('when', type: 'string')]
    private string $when;

    #[ORM\Column('specific_date', type: 'datetimetz', nullable: true)]
    private ?\DateTimeInterface $specificDate = null;

    #[ORM\Column('refund_type', type: 'string')]
    private string $refundType;

    #[ORM\Column('comment', type: 'string', nullable: true)]
    private ?string $comment;

    #[ORM\ManyToOne(className: Subscription::class)]
    private Subscription $subscription;

    #[ORM\ManyToOne(className: BillingAdminInterface::class)]
    private BillingAdminInterface $billingAdmin;

    #[ORM\Column('created_at', type: 'datetimetz')]
    private \DateTimeInterface $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getWhen(): string
    {
        return $this->when;
    }

    public function setWhen(string $when): void
    {
        $this->when = $when;
    }

    public function getSpecificDate(): ?\DateTimeInterface
    {
        return $this->specificDate;
    }

    public function setSpecificDate(?\DateTimeInterface $specificDate): void
    {
        $this->specificDate = $specificDate;
    }

    public function getRefundType(): string
    {
        return $this->refundType;
    }

    public function setRefundType(string $refundType): void
    {
        $this->refundType = $refundType;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getBillingAdmin(): BillingAdminInterface
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(BillingAdminInterface $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
