<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\Provider;

use BillaBear\Repository\SettingsRepositoryInterface;
use Obol\Factory;
use Obol\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProviderFactory
{
    public function __construct(
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        private array $obolConfig,
        private SettingsRepositoryInterface $obolSettingsRepository,
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

        return Factory::create($config);
    }
}
