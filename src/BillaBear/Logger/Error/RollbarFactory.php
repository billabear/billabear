<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Logger\Error;

use Rollbar\RollbarLogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RollbarFactory
{
    public function __construct(
        #[Autowire('%rollbar.api_key%')]
        private readonly string $apiKey,
        #[Autowire('%rollbar.env%')]
        private readonly string $env,
        #[Autowire('%rollbar.commit_hash%')]
        private readonly string $gitHash,
    ) {
    }

    public function create(): RollbarLogger
    {
        $checkIgnoreCallback = function ($isUncaught, $toLog, $payload) {
            if ($toLog instanceof HttpException) {
                return false;
            }

            return true;
        };

        $config = [
            'access_token' => $this->apiKey,
            'environment' => $this->env,
            'code_version' => $this->gitHash,
            'root' => dirname(dirname(dirname(dirname(dirname(__FILE__))))),
            'check_ignore' => $checkIgnoreCallback,
        ];

        return new RollbarLogger($config);
    }
}
