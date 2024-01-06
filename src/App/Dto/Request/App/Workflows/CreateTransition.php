<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Workflows;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTransition
{
    #[Assert\Choice(['cancel_subscription', 'create_subscription', 'create_payment'])]
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
