<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Logger\Error;

use Monolog\Handler\RollbarHandler as BaseRollbarHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Rollbar\RollbarLogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RollbarHandler extends BaseRollbarHandler
{
    public function __construct(
        #[Autowire('%rollbar.enabled%')]
        private bool $enabled,
        RollbarLogger $rollbarLogger, int|Level|string $level = Level::Error, bool $bubble = true)
    {
        parent::__construct($rollbarLogger, $level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        if (!$this->enabled) {
            return;
        }

        parent::write($record);
    }
}
