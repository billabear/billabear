<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Database\Doctrine;

use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;

class HypertableSchemaListener
{
    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs): void
    {
        $table = $eventArgs->getClassTable();
        $classMetadata = $eventArgs->getClassMetadata();
        $schema = $eventArgs->getSchema();

        $reflectionClass = $classMetadata->getReflectionClass();

        if ($reflectionClass->getAttributes(Hypertable::class)) {
            $hypertableAttr = $reflectionClass->getAttributes(Hypertable::class)[0]->newInstance();
            $timeColumn = $hypertableAttr->timeColumn;

            $table->addOption('hypertable', true);
            $table->addOption('hypertable_index', $timeColumn);
        }
    }
}
