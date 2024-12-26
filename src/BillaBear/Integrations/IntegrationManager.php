<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations;

use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class IntegrationManager
{
    /**
     * @param IntegrationInterface[]|iterable $integrations
     */
    public function __construct(
        #[TaggedIterator('billabear.integration')]
        private iterable $integrations,
    ) {
    }

    public function getIntegration(string $name): IntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration->getName() === $name) {
                return $integration;
            }
        }

        throw new \RuntimeException(sprintf('Integration "%s" not found', $name));
    }

    public function getAccountingIntegration(string $name): IntegrationInterface&AccountingIntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration->getName() === $name && $integration instanceof AccountingIntegrationInterface) {
                return $integration;
            }
        }

        throw new \RuntimeException(sprintf('Integration "%s" not found', $name));
    }

    /**
     * @return AccountingIntegrationInterface[]
     */
    public function getAccountingIntegrations(): array
    {
        $output = [];

        foreach ($this->integrations as $integration) {
            if (IntegrationType::ACCOUNTING === $integration->getType()) {
                $output[] = $integration;
            }
        }

        return $output;
    }

    public function getCustomerSupportIntegrations(): array
    {
        $output = [];

        foreach ($this->integrations as $integration) {
            if (IntegrationType::CUSTOMER_SUPPORT === $integration->getType()) {
                $output[] = $integration;
            }
        }

        return $output;
    }
}
