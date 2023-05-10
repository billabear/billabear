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
final class Version20230510172410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refund_amount_daily_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN refund_amount_daily_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refund_amount_monthly_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN refund_amount_monthly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refund_amount_yearly_stats (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN refund_amount_yearly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refund_created_process (id UUID NOT NULL, payment_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C12A211C4C3A3BB ON refund_created_process (payment_id)');
        $this->addSql('COMMENT ON COLUMN refund_created_process.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN refund_created_process.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE refund_created_process ADD CONSTRAINT FK_C12A211C4C3A3BB FOREIGN KEY (payment_id) REFERENCES refund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_amount_daily_stats ADD brand_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment_amount_monthly_stats ADD brand_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment_amount_yearly_stats ADD brand_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription_daily_stats ADD brand_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription_monthly_stats ADD brand_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription_yearly_stats ADD brand_code VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE refund_created_process DROP CONSTRAINT FK_C12A211C4C3A3BB');
        $this->addSql('DROP TABLE refund_amount_daily_stats');
        $this->addSql('DROP TABLE refund_amount_monthly_stats');
        $this->addSql('DROP TABLE refund_amount_yearly_stats');
        $this->addSql('DROP TABLE refund_created_process');
        $this->addSql('ALTER TABLE subscription_monthly_stats DROP brand_code');
        $this->addSql('ALTER TABLE subscription_yearly_stats DROP brand_code');
        $this->addSql('ALTER TABLE payment_amount_yearly_stats DROP brand_code');
        $this->addSql('ALTER TABLE payment_amount_monthly_stats DROP brand_code');
        $this->addSql('ALTER TABLE payment_amount_daily_stats DROP brand_code');
        $this->addSql('ALTER TABLE subscription_daily_stats DROP brand_code');
    }
}
