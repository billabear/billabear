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
final class Version20240311203603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type');
        $this->addSql('COMMENT ON COLUMN product.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD84042C99 ON product (tax_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD84042C99');
        $this->addSql('DROP INDEX IDX_D34A04AD84042C99');
        $this->addSql('ALTER TABLE product ADD tax_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type_id');
    }
}
