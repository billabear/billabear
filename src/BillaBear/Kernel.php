<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear;

use BillaBear\DependencyInjection\Compiler\WorkflowPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    public const VERSION = '2025.02.01';
    public const VERSION_ID = '20250101';

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WorkflowPass());
    }
}
