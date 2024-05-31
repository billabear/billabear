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
final class Version20240530221458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE state (id UUID NOT NULL, country_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, threshold BIGINT NOT NULL, has_nexus BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A393D2FBF92F3E70 ON state (country_id)');
        $this->addSql('COMMENT ON COLUMN state.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE state_tax_rule (id UUID NOT NULL, state_id UUID DEFAULT NULL, tax_type_id UUID DEFAULT NULL, tax_rate DOUBLE PRECISION NOT NULL, is_default BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65A4C5935D83CC1 ON state_tax_rule (state_id)');
        $this->addSql('CREATE INDEX IDX_65A4C59384042C99 ON state_tax_rule (tax_type_id)');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.state_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state_tax_rule ADD CONSTRAINT FK_65A4C5935D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state_tax_rule ADD CONSTRAINT FK_65A4C59384042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE economic_area_member DROP CONSTRAINT fk_4bda007af92f3e70');
        $this->addSql('ALTER TABLE economic_area_member DROP CONSTRAINT fk_4bda007af7431d7a');
        $this->addSql('DROP TABLE economic_area_member');
        $this->addSql('DROP TABLE economic_area');
        $this->addSql('ALTER TABLE checkout_line ADD tax_state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD tax_state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line ADD tax_state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_state VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE economic_area_member (id UUID NOT NULL, country_id UUID DEFAULT NULL, economic_area_id UUID DEFAULT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, left_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4bda007af7431d7a ON economic_area_member (economic_area_id)');
        $this->addSql('CREATE INDEX idx_4bda007af92f3e70 ON economic_area_member (country_id)');
        $this->addSql('COMMENT ON COLUMN economic_area_member.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN economic_area_member.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN economic_area_member.economic_area_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE economic_area (id UUID NOT NULL, name VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, threshold BIGINT NOT NULL, start_of_tax_year VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN economic_area.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE economic_area_member ADD CONSTRAINT fk_4bda007af92f3e70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE economic_area_member ADD CONSTRAINT fk_4bda007af7431d7a FOREIGN KEY (economic_area_id) REFERENCES economic_area (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state DROP CONSTRAINT FK_A393D2FBF92F3E70');
        $this->addSql('ALTER TABLE state_tax_rule DROP CONSTRAINT FK_65A4C5935D83CC1');
        $this->addSql('ALTER TABLE state_tax_rule DROP CONSTRAINT FK_65A4C59384042C99');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE state_tax_rule');
        $this->addSql('ALTER TABLE invoice_line DROP tax_state');
        $this->addSql('ALTER TABLE quote_line DROP tax_state');
        $this->addSql('ALTER TABLE receipt_line DROP tax_state');
        $this->addSql('ALTER TABLE payment DROP state');
        $this->addSql('ALTER TABLE checkout_line DROP tax_state');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_state');
    }
}
