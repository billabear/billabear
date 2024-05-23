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
final class Version20240523191156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE economic_area (id UUID NOT NULL, name VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, threshold BIGINT NOT NULL, start_of_tax_year VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN economic_area.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE economic_area_member (id UUID NOT NULL, country_id UUID DEFAULT NULL, economic_area_id UUID DEFAULT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, left_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4BDA007AF92F3E70 ON economic_area_member (country_id)');
        $this->addSql('CREATE INDEX IDX_4BDA007AF7431D7A ON economic_area_member (economic_area_id)');
        $this->addSql('COMMENT ON COLUMN economic_area_member.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN economic_area_member.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN economic_area_member.economic_area_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE economic_area_member ADD CONSTRAINT FK_4BDA007AF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE economic_area_member ADD CONSTRAINT FK_4BDA007AF7431D7A FOREIGN KEY (economic_area_id) REFERENCES economic_area (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE settings ADD system_settings_stripe_public_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_stripe_private_key VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE economic_area_member DROP CONSTRAINT FK_4BDA007AF92F3E70');
        $this->addSql('ALTER TABLE economic_area_member DROP CONSTRAINT FK_4BDA007AF7431D7A');
        $this->addSql('DROP TABLE economic_area');
        $this->addSql('DROP TABLE economic_area_member');
        $this->addSql('ALTER TABLE settings DROP system_settings_stripe_public_key');
        $this->addSql('ALTER TABLE settings DROP system_settings_stripe_private_key');
    }
}
