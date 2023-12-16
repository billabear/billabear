<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DependencyInjection\Compiler;

use App\Workflow\Places\PlacesProvider;
use App\Workflow\TransitionHandlers\DynamicHandlerProvider;
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
        if (!$container->has(DynamicHandlerProvider::class)) {
            return;
        }

        $definition = $container->findDefinition(DynamicHandlerProvider::class);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('app.workflow.handler');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
