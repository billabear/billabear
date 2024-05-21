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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521191149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country ALTER threshold TYPE BIGINT');
        $this->addSql('ALTER TABLE receipt_line ALTER reverse_charge DROP DEFAULT');
        $this->addSql('ALTER TABLE receipt_line ALTER reverse_charge SET NOT NULL');
        $this->addSql('ALTER TABLE templates ALTER locale DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE country ALTER threshold TYPE INT');
        $this->addSql('ALTER TABLE templates ALTER locale SET DEFAULT \'en\'');
        $this->addSql('ALTER TABLE receipt_line ALTER reverse_charge SET DEFAULT false');
        $this->addSql('ALTER TABLE receipt_line ALTER reverse_charge DROP NOT NULL');
    }
}
