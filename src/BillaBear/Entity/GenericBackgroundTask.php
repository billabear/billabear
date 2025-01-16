<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Enum\GenericTask;
use BillaBear\Enum\GenericTaskStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(fields: ['status'])]
#[ORM\Table(name: 'generic_background_task')]
class GenericBackgroundTask
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    protected $id;

    #[ORM\Column(enumType: GenericTaskStatus::class)]
    private GenericTaskStatus $status;

    #[ORM\Column(enumType: GenericTask::class)]
    private GenericTask $task;

    #[ORM\Column(type: 'json')]
    private array $meta = [];

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getStatus(): GenericTaskStatus
    {
        return $this->status;
    }

    public function setStatus(GenericTaskStatus $status): void
    {
        $this->status = $status;
    }

    public function getTask(): GenericTask
    {
        return $this->task;
    }

    public function setTask(GenericTask $task): void
    {
        $this->task = $task;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
