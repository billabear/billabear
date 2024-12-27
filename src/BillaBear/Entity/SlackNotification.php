<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Notification\Slack\SlackNotificationEvent;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Athena\Entity\DeletableInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'slack_notification')]
class SlackNotification implements DeletableInterface
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $deletedAt;

    #[ORM\Column(type: 'boolean')]
    protected $isDeleted = false;
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(enumType: SlackNotificationEvent::class)]
    private SlackNotificationEvent $event;

    #[ORM\ManyToOne(targetEntity: SlackWebhook::class)]
    private SlackWebhook $slackWebhook;

    #[ORM\Column(type: 'string', length: 10000)]
    private string $messageTemplate;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->isDeleted = true;
        $this->deletedAt = new \DateTime('Now');

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->isDeleted = false;
        $this->deletedAt = null;

        return $this;
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;

        return $this;
    }

    public function getSlackWebhook(): SlackWebhook
    {
        return $this->slackWebhook;
    }

    public function setSlackWebhook(SlackWebhook $slackWebhook): void
    {
        $this->slackWebhook = $slackWebhook;
    }

    public function getEvent(): SlackNotificationEvent
    {
        return $this->event;
    }

    public function setEvent(SlackNotificationEvent $event): void
    {
        $this->event = $event;
    }

    public function getMessageTemplate(): string
    {
        return $this->messageTemplate;
    }

    public function setMessageTemplate(string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }
}
