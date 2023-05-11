<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511131536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE charge_back_amount_daily_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN charge_back_amount_daily_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE charge_back_amount_monthly_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN charge_back_amount_monthly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE charge_back_amount_yearly_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN charge_back_amount_yearly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE charge_back_creation (id UUID NOT NULL, charge_back_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3394E461BDB3CB55 ON charge_back_creation (charge_back_id)');
        $this->addSql('COMMENT ON COLUMN charge_back_creation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN charge_back_creation.charge_back_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE charge_back_creation ADD CONSTRAINT FK_3394E461BDB3CB55 FOREIGN KEY (charge_back_id) REFERENCES charge_back (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refund_created_process DROP CONSTRAINT fk_c12a211c4c3a3bb');
        $this->addSql('DROP INDEX idx_c12a211c4c3a3bb');
        $this->addSql('ALTER TABLE refund_created_process RENAME COLUMN payment_id TO refund_id');
        $this->addSql('ALTER TABLE refund_created_process ADD CONSTRAINT FK_C12A211C189801D5 FOREIGN KEY (refund_id) REFERENCES refund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C12A211C189801D5 ON refund_created_process (refund_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE charge_back_creation DROP CONSTRAINT FK_3394E461BDB3CB55');
        $this->addSql('DROP TABLE charge_back_amount_daily_stats');
        $this->addSql('DROP TABLE charge_back_amount_monthly_stats');
        $this->addSql('DROP TABLE charge_back_amount_yearly_stats');
        $this->addSql('DROP TABLE charge_back_creation');
        $this->addSql('ALTER TABLE refund_created_process DROP CONSTRAINT FK_C12A211C189801D5');
        $this->addSql('DROP INDEX IDX_C12A211C189801D5');
        $this->addSql('ALTER TABLE refund_created_process RENAME COLUMN refund_id TO payment_id');
        $this->addSql('ALTER TABLE refund_created_process ADD CONSTRAINT fk_c12a211c4c3a3bb FOREIGN KEY (payment_id) REFERENCES refund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c12a211c4c3a3bb ON refund_created_process (payment_id)');
    }
}
