<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501124936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $uuid = Uuid::uuid4();
        $this->addSql('INSERT INTO tax_type (id, name) VALUES (:id, :name)', ['id' => (string) $uuid, 'name' => 'Default']);
        $this->addSql('UPDATE product SET tax_type_id=:id', ['id' => (string) $uuid]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM tax_type WHERE name = :name', ['name' => 'Default']);
        $this->addSql('UPDATE product SET tax_type_id=null');
    }
}
