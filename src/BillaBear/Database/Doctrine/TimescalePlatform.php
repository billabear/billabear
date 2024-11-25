<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Database\Doctrine;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Table;

class TimescalePlatform extends PostgreSQLPlatform
{
    public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES): array
    {
        // Generate the regular SQL for creating the table
        // Check if the table should be a hypertable

        return parent::getCreateTableSQL($table, $createFlags);
    }

    public function getCreateTablesSQL(array $tables): array
    {
        $sql = parent::getCreateTablesSQL($tables);

        $sql[] = 'create extension if not exists timescaledb;';

        foreach ($tables as $table) {
            if ($this->isHypertable($table)) {
                $primaryColumnName = $this->getPrimaryColumnName($table);
                // Add TimescaleDB's `create_hypertable` SQL command
                $hypertableSql = sprintf(
                    "SELECT create_hypertable('%s', by_range('%s'));",
                    $table->getName(),
                    $primaryColumnName
                );

                $sql[] = $hypertableSql;
            }
        }

        return $sql;
    }

    protected function isHypertable(Table $table): bool
    {
        // Check for the custom attribute on the Table object
        return $table->hasOption('hypertable') && true === $table->getOption('hypertable');
    }

    protected function getPrimaryColumnName(Table $table): string
    {
        if (!$table->hasOption('hypertable_index')) {
            throw new Exception('No hypertable index defined');
        }

        return $table->getOption('hypertable_index');
    }
}
