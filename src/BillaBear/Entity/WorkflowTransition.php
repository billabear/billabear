<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\WorkflowType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'workflow_transition')]
class WorkflowTransition implements PlaceInterface
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string', enumType: WorkflowType::class)]
    private WorkflowType $workflow;

    #[ORM\Column(type: 'integer')]
    private int $priority;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $handlerName;

    #[ORM\Column(type: 'json')]
    private array $handlerOptions = [];

    #[ORM\OneToMany(targetEntity: WorkflowTransitionRule::class, mappedBy: 'workflowTransition', cascade: ['persist', 'remove'])]
    private array|Collection $rules;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getWorkflow(): WorkflowType
    {
        return $this->workflow;
    }

    public function setWorkflow(WorkflowType $workflow): void
    {
        $this->workflow = $workflow;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHandlerName(): string
    {
        return $this->handlerName;
    }

    public function setHandlerName(string $handlerName): void
    {
        $this->handlerName = $handlerName;
    }

    public function getHandlerOptions(): array
    {
        return $this->handlerOptions;
    }

    public function setHandlerOptions(array $handlerOptions): void
    {
        $this->handlerOptions = $handlerOptions;
    }

    public function getRules(): array|Collection
    {
        return $this->rules;
    }

    public function setRules(array|Collection $rules): void
    {
        $this->rules = $rules;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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

    public function getToTransitionName(): string
    {
        return sprintf('transition_%s', $this->name);
    }
}
