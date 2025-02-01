<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations;

use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use BillaBear\Integrations\Crm\CrmIntegrationInterface;
use BillaBear\Integrations\CustomerSupport\CustomerSupportIntegrationInterface;
use BillaBear\Integrations\Newsletter\NewsletterIntegrationInterface;
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

    public function getAccountingIntegration(string $name): AccountingIntegrationInterface&IntegrationInterface
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

    public function getCrmIntegration(string $name): CrmIntegrationInterface&IntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration->getName() === $name && $integration instanceof CrmIntegrationInterface) {
                return $integration;
            }
        }

        throw new \RuntimeException(sprintf('Integration "%s" not found', $name));
    }

    /**
     * @return CrmIntegrationInterface[]
     */
    public function getCrmIntegrations(): array
    {
        $output = [];

        foreach ($this->integrations as $integration) {
            if (IntegrationType::CRM === $integration->getType()) {
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

    public function getCustomerSupportIntegration(string $name): CustomerSupportIntegrationInterface&IntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration->getName() === $name && $integration instanceof CustomerSupportIntegrationInterface) {
                return $integration;
            }
        }

        throw new \RuntimeException(sprintf('Integration "%s" not found', $name));
    }

    public function getNewsletterIntegrations(): array
    {
        $output = [];

        foreach ($this->integrations as $integration) {
            if (IntegrationType::NEWSLETTER === $integration->getType()) {
                $output[] = $integration;
            }
        }

        return $output;
    }

    public function getNewsletterIntegration(string $name): IntegrationInterface&NewsletterIntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration->getName() === $name && $integration instanceof NewsletterIntegrationInterface) {
                return $integration;
            }
        }

        throw new \RuntimeException(sprintf('Integration "%s" not found', $name));
    }
}
