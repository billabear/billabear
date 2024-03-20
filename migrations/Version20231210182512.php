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
final class Version20231210182512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 2024.01.01';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE workflow_transition (id UUID NOT NULL, workflow VARCHAR(255) NOT NULL, priority INT NOT NULL, name VARCHAR(255) NOT NULL, handler_name VARCHAR(255) NOT NULL, handler_options JSON NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN workflow_transition.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE workflow_transition_rule (id UUID NOT NULL, workflow_transition_id UUID DEFAULT NULL, condition VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A04630361D3B524A ON workflow_transition_rule (workflow_transition_id)');
        $this->addSql('COMMENT ON COLUMN workflow_transition_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN workflow_transition_rule.workflow_transition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE workflow_transition_rule ADD CONSTRAINT FK_A04630361D3B524A FOREIGN KEY (workflow_transition_id) REFERENCES workflow_transition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(255) NOT NULL, iso_code VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, threshold INT NOT NULL, revenue_for_tax_year INT DEFAULT NULL, start_of_tax_year TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN country.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tax_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tax_type.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE workflow_transition_rule DROP CONSTRAINT FK_A04630361D3B524A');
        $this->addSql('DROP TABLE workflow_transition');
        $this->addSql('DROP TABLE workflow_transition_rule');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE tax_type');
    }
}
