<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DependencyInjection\Compiler;

use App\Workflow\Places\PlacesProvider;
use App\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkflowPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(PlacesProvider::class)) {
            return;
        }

        $definition = $container->findDefinition(PlacesProvider::class);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('app.workflow.place');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addPlace', [new Reference($id)]);
        }

        // always first check if the primary service is defined
        if (!$container->has(DynamicTransitionHandlerProvider::class)) {
            return;
        }

        $definition = $container->findDefinition(DynamicTransitionHandlerProvider::class);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('app.workflow.handler');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
