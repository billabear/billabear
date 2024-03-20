<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129185816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country_tax_rule (id UUID NOT NULL, country_id UUID DEFAULT NULL, tax_type_id UUID DEFAULT NULL, tax_rate DOUBLE PRECISION NOT NULL, is_default BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_699DF289F92F3E70 ON country_tax_rule (country_id)');
        $this->addSql('CREATE INDEX IDX_699DF28984042C99 ON country_tax_rule (tax_type_id)');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE country_tax_rule ADD CONSTRAINT FK_699DF289F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_tax_rule ADD CONSTRAINT FK_699DF28984042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country ADD in_eu BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF289F92F3E70');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF28984042C99');
        $this->addSql('DROP TABLE country_tax_rule');
        $this->addSql('ALTER TABLE country DROP in_eu');
    }
}
