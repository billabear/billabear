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
final class Version20230313105500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e9296cd8ae');
        $this->addSql('CREATE TABLE customers (id UUID NOT NULL, reference VARCHAR(255) DEFAULT NULL, external_customer_reference VARCHAR(255) NOT NULL, billing_email VARCHAR(255) NOT NULL, subscription_plan_name VARCHAR(255) DEFAULT NULL, subscription_payment_schedule VARCHAR(255) DEFAULT NULL, subscription_active BOOLEAN DEFAULT NULL, subscription_status VARCHAR(255) DEFAULT NULL, subscription_amount INT DEFAULT NULL, subscription_currency VARCHAR(255) DEFAULT NULL, subscription_started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_seats INT DEFAULT NULL, billing_address_company_name VARCHAR(255) DEFAULT NULL, billing_address_street_line_one VARCHAR(255) DEFAULT NULL, billing_address_street_line_two VARCHAR(255) DEFAULT NULL, billing_address_city VARCHAR(255) DEFAULT NULL, billing_address_region VARCHAR(255) DEFAULT NULL, billing_address_country VARCHAR(255) DEFAULT NULL, billing_address_postcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21E2ED5959 ON customers (billing_email)');
        $this->addSql('COMMENT ON COLUMN customers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE payment-refund (id UUID NOT NULL, customer_id UUID DEFAULT NULL, provider VARCHAR(255) NOT NULL, payment_reference VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, refunded BOOLEAN NOT NULL, completed BOOLEAN NOT NULL, charged_back BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D9395C3F3 ON payment-refund (customer_id)');
        $this->addSql('COMMENT ON COLUMN payment-refund.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment-refund.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE payment_details (id UUID NOT NULL, customer_id UUID DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, stored_customer_reference VARCHAR(255) DEFAULT NULL, stored_payment_reference VARCHAR(255) DEFAULT NULL, default_payment_option BOOLEAN NOT NULL, brand VARCHAR(255) DEFAULT NULL, last_four VARCHAR(255) DEFAULT NULL, expiry_month VARCHAR(255) DEFAULT NULL, expiry_year VARCHAR(255) DEFAULT NULL, is_deleted BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B6F05609395C3F3 ON payment_details (customer_id)');
        $this->addSql('COMMENT ON COLUMN payment_details.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_details.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment-refund ADD CONSTRAINT FK_6D28840D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_details ADD CONSTRAINT FK_6B6F05609395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT fk_e05e9270a76ed395');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT fk_e05e9270c58dad6e');
        $this->addSql('ALTER TABLE team_invite_codes DROP CONSTRAINT fk_e05e9270296cd8ae');
        $this->addSql('ALTER TABLE parthenon_ab_experiment_variant DROP CONSTRAINT fk_284e93bdff444c8');
        $this->addSql('DROP TABLE parthenon_ab_experiments');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE team_invite_codes');
        $this->addSql('DROP TABLE parthenon_ab_experiment_variant');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP INDEX idx_1483a5e9296cd8ae');
        $this->addSql('ALTER TABLE users DROP team_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE parthenon_ab_experiments (id UUID NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, desired_result VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_6c53d6955e237e06 ON parthenon_ab_experiments (name)');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiments.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE teams (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, subscription_price_id VARCHAR(255) DEFAULT NULL, subscription_plan_name VARCHAR(255) DEFAULT NULL, subscription_payment_schedule VARCHAR(255) DEFAULT NULL, subscription_active BOOLEAN DEFAULT NULL, subscription_status VARCHAR(255) DEFAULT NULL, subscription_started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_payment_id VARCHAR(255) DEFAULT NULL, subscription_customer_id VARCHAR(255) DEFAULT NULL, subscription_checkout_id VARCHAR(255) DEFAULT NULL, subscription_seats INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN teams.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE team_invite_codes (id UUID NOT NULL, user_id UUID DEFAULT NULL, invited_user_id UUID DEFAULT NULL, team_id UUID DEFAULT NULL, code VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancelled BOOLEAN NOT NULL, role VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e05e9270296cd8ae ON team_invite_codes (team_id)');
        $this->addSql('CREATE INDEX idx_e05e9270c58dad6e ON team_invite_codes (invited_user_id)');
        $this->addSql('CREATE INDEX idx_e05e9270a76ed395 ON team_invite_codes (user_id)');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.invited_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN team_invite_codes.team_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE parthenon_ab_experiment_variant (id UUID NOT NULL, experiment_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, percentage INT NOT NULL, is_default BOOLEAN NOT NULL, stats_number_of_sessions INT NOT NULL, stats_number_of_conversions INT NOT NULL, stats_conversion_percentage DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX search_idx ON parthenon_ab_experiment_variant (name)');
        $this->addSql('CREATE INDEX idx_284e93bdff444c8 ON parthenon_ab_experiment_variant (experiment_id)');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiment_variant.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN parthenon_ab_experiment_variant.experiment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE notification (id UUID NOT NULL, message_template VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_read BOOLEAN NOT NULL, link_url_name VARCHAR(255) NOT NULL, link_url_variables JSON NOT NULL, link_is_raw BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT fk_e05e9270a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT fk_e05e9270c58dad6e FOREIGN KEY (invited_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_invite_codes ADD CONSTRAINT fk_e05e9270296cd8ae FOREIGN KEY (team_id) REFERENCES teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parthenon_ab_experiment_variant ADD CONSTRAINT fk_284e93bdff444c8 FOREIGN KEY (experiment_id) REFERENCES parthenon_ab_experiments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment-refund DROP CONSTRAINT FK_6D28840D9395C3F3');
        $this->addSql('ALTER TABLE payment_details DROP CONSTRAINT FK_6B6F05609395C3F3');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE payment-refund');
        $this->addSql('DROP TABLE payment_details');
        $this->addSql('ALTER TABLE users ADD team_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN users.team_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_1483a5e9296cd8ae FOREIGN KEY (team_id) REFERENCES teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1483a5e9296cd8ae ON users (team_id)');
    }
}
