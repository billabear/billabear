<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Logger\Error;

use Rollbar\RollbarLogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class RollbarFactory
{
    public function __construct(
        #[Autowire('%rollbar.api_key%')]
        private string $apiKey,
        #[Autowire('%rollbar.env%')]
        private string $env,
        #[Autowire('%rollbar.commit_hash%')]
        private string $gitHash,
    ) {
    }

    public function create(): RollbarLogger
    {
        $checkIgnoreCallback = function ($isUncaught, $toLog, $payload) {
            if ($toLog instanceof HttpException) {
                return true;
            }

            return false;
        };

        $config = [
            'access_token' => $this->apiKey,
            'environment' => $this->env,
            'code_version' => $this->gitHash,
            'root' => dirname(__FILE__, 5),
            'check_ignore' => $checkIgnoreCallback,
        ];

        return new RollbarLogger($config);
    }
}
