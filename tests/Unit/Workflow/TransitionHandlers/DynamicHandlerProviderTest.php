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

namespace App\Tests\Unit\Workflow\TransitionHandlers;

use App\Workflow\TransitionHandlers\DynamicHandlerInterface;
use App\Workflow\TransitionHandlers\DynamicHandlerProvider;
use PHPUnit\Framework\TestCase;

class DynamicHandlerProviderTest extends TestCase
{
    public function testReturnsHandler(): void
    {
        $handlerOne = $this->createMock(DynamicHandlerInterface::class);
        $handlerTwo = $this->createMock(DynamicHandlerInterface::class);

        $handlerOne->method('getName')->willReturn('one');
        $handlerTwo->method('getName')->willReturn('two');

        $subject = new DynamicHandlerProvider();
        $subject->addHandler($handlerOne);
        $subject->addHandler($handlerTwo);

        $this->assertSame($handlerOne, $subject->getHandlerByName('one'));
        $this->assertSame($handlerTwo, $subject->getHandlerByName('two'));
    }
}
