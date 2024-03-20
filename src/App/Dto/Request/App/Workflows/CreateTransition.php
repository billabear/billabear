<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Workflows;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTransition
{
    #[Assert\Choice(['cancel_subscription', 'create_subscription', 'create_payment', 'create_chargeback', 'create_refund'])]
    #[Assert\Type('string')]
    private $workflow;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    private $priority;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $handler;

    #[SerializedName('handler_options')]
    private $handlerOptions;

    public function getWorkflow()
    {
        return $this->workflow;
    }

    public function setWorkflow($workflow): void
    {
        $this->workflow = $workflow;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler): void
    {
        $this->handler = $handler;
    }

    public function getHandlerOptions()
    {
        return $this->handlerOptions;
    }

    public function setHandlerOptions($handlerOptions): void
    {
        $this->handlerOptions = $handlerOptions;
    }
}
