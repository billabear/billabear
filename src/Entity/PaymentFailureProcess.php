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
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'payment_failure_process')]
class PaymentFailureProcess
{
    public const DEFAULT_NEXT_ATTEMPT = '+3 days';
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: PaymentAttempt::class)]
    private PaymentAttempt $paymentAttempt;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private ?Customer $customer = null;

    #[ORM\Column('state', type: 'string')]
    private string $state;

    #[ORM\Column('retry_count', type: 'integer')]
    private int $retryCount;

    #[ORM\Column('resolved', type: 'boolean')]
    private bool $resolved;

    #[ORM\Column('next_attempt_at', type: 'datetimetz', nullable: true)]
    private ?\DateTimeInterface $nextAttemptAt;

    #[ORM\Column('created_at', type: 'datetimetz')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column('updated_at', type: 'datetimetz')]
    private \DateTimeInterface $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getPaymentAttempt(): PaymentAttempt
    {
        return $this->paymentAttempt;
    }

    public function setPaymentAttempt(PaymentAttempt $paymentAttempt): void
    {
        $this->paymentAttempt = $paymentAttempt;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function setRetryCount(int $retryCount): void
    {
        $this->retryCount = $retryCount;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): void
    {
        $this->resolved = $resolved;
    }

    public function getNextAttemptAt(): ?\DateTimeInterface
    {
        return $this->nextAttemptAt;
    }

    public function setNextAttemptAt(?\DateTimeInterface $nextAttemptAt): void
    {
        $this->nextAttemptAt = $nextAttemptAt;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
