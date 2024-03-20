<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity\Processes;

use App\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\PaymentCard;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'expiring_card_process')]
class ExpiringCardProcess
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\OneToOne(targetEntity: PaymentCard::class)]
    private PaymentCard $paymentCard;

    #[ORM\Column('state', type: 'string')]
    private string $state;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column('updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column('subscription_charged_at', type: 'datetime')]
    private \DateTimeInterface $subscriptionChargedAt;

    #[ORM\Column('error', type: 'string', nullable: true)]
    private ?string $error = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getPaymentCard(): PaymentCard
    {
        return $this->paymentCard;
    }

    public function setPaymentCard(PaymentCard $paymentCard): void
    {
        $this->paymentCard = $paymentCard;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
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

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function getSubscriptionChargedAt(): \DateTimeInterface
    {
        return $this->subscriptionChargedAt;
    }

    public function setSubscriptionChargedAt(\DateTimeInterface $subscriptionChargedAt): void
    {
        $this->subscriptionChargedAt = $subscriptionChargedAt;
    }
}
