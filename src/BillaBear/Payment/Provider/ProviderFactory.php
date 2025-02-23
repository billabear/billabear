<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\Provider;

use BillaBear\Repository\SettingsRepositoryInterface;
use Obol\Factory;
use Obol\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProviderFactory
{
    public function __construct(
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        private array $obolConfig,
        private SettingsRepositoryInterface $obolSettingsRepository,
        private LoggerInterface $obolLogger,
    ) {
    }

    public function create(): ProviderInterface
    {
        if (!isset($this->obolConfig['api_key']) || empty($this->obolConfig['api_key'])) {
            $config = [
                'provider' => 'stripe',
                'api_key' => $this->obolSettingsRepository->getDefaultSettings()->getSystemSettings()->getStripePrivateKey(),
            ];
        } else {
            $config = $this->obolConfig;
        }

        return Factory::create($config, $this->obolLogger);
    }

    public function getApiKey(): ?string
    {
        if (!isset($this->obolConfig['api_key']) || empty($this->obolConfig['api_key'])) {
            return $this->obolSettingsRepository->getDefaultSettings()->getSystemSettings()->getStripePrivateKey();
        } else {
            return $this->obolConfig['api_key'] ?? null;
        }
    }
}
