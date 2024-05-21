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
final class Version20240521165727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2024.01.01';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(255) NOT NULL, iso_code VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, threshold INT NOT NULL, start_of_tax_year VARCHAR(255) DEFAULT NULL, in_eu BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN country.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE country_tax_rule (id UUID NOT NULL, country_id UUID DEFAULT NULL, tax_type_id UUID DEFAULT NULL, tax_rate DOUBLE PRECISION NOT NULL, is_default BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_699DF289F92F3E70 ON country_tax_rule (country_id)');
        $this->addSql('CREATE INDEX IDX_699DF28984042C99 ON country_tax_rule (tax_type_id)');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE slack_notification (id UUID NOT NULL, slack_webhook_id UUID DEFAULT NULL, event VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FFC6C91B2A257EF ON slack_notification (slack_webhook_id)');
        $this->addSql('COMMENT ON COLUMN slack_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN slack_notification.slack_webhook_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE slack_webhook (id UUID NOT NULL, name VARCHAR(255) NOT NULL, webhook_url VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN slack_webhook.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tax_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, is_default BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tax_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE workflow_transition (id UUID NOT NULL, workflow VARCHAR(255) NOT NULL, priority INT NOT NULL, name VARCHAR(255) NOT NULL, handler_name VARCHAR(255) NOT NULL, handler_options JSON NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN workflow_transition.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE workflow_transition_rule (id UUID NOT NULL, workflow_transition_id UUID DEFAULT NULL, condition VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A04630361D3B524A ON workflow_transition_rule (workflow_transition_id)');
        $this->addSql('COMMENT ON COLUMN workflow_transition_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN workflow_transition_rule.workflow_transition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE country_tax_rule ADD CONSTRAINT FK_699DF289F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_tax_rule ADD CONSTRAINT FK_699DF28984042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE slack_notification ADD CONSTRAINT FK_4FFC6C91B2A257EF FOREIGN KEY (slack_webhook_id) REFERENCES slack_webhook (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workflow_transition_rule ADD CONSTRAINT FK_A04630361D3B524A FOREIGN KEY (workflow_transition_id) REFERENCES workflow_transition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN checkout_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D412884042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3A4D412884042C99 ON checkout_line (tax_type_id)');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7D84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_46316A7D84042C99 ON checkout_session_line (tax_type_id)');
        $this->addSql('ALTER TABLE customers DROP tax_rate_digital');
        $this->addSql('DROP INDEX uniq_5ae3e774fda273ec');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('ALTER TABLE exchange_rates ADD original_currency VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE exchange_rates SET original_currency=(SELECT system_settings_main_currency FROM settings WHERE tag = \'default\')');
        $this->addSql('ALTER TABLE exchange_rates ALTER original_currency SET NOT NULL');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code, original_currency)');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN invoice_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D69384042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3D1D69384042C99 ON invoice_line (tax_type_id)');
        $this->addSql('ALTER TABLE payment ADD country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD physical BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type');
        $this->addSql('COMMENT ON COLUMN product.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD84042C99 ON product (tax_type_id)');
        $this->addSql('ALTER TABLE quote_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line DROP tax_type');
        $this->addSql('COMMENT ON COLUMN quote_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7C84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_43F3EB7C84042C99 ON quote_line (tax_type_id)');
        $this->addSql('ALTER TABLE receipt ALTER vat_percentage DROP NOT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD reverse_charge BOOLEAN DEFAULT false');
        $this->addSql('ALTER TABLE receipt_line ALTER vat_percentage DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN receipt_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receipt_line ADD CONSTRAINT FK_476F8F7A84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_476F8F7A84042C99 ON receipt_line (tax_type_id)');
        $this->addSql('ALTER TABLE templates ADD locale VARCHAR(255) NOT NULL DEFAULT \'en\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D412884042C99');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7D84042C99');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D69384042C99');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD84042C99');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7C84042C99');
        $this->addSql('ALTER TABLE receipt_line DROP CONSTRAINT FK_476F8F7A84042C99');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF289F92F3E70');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF28984042C99');
        $this->addSql('ALTER TABLE slack_notification DROP CONSTRAINT FK_4FFC6C91B2A257EF');
        $this->addSql('ALTER TABLE workflow_transition_rule DROP CONSTRAINT FK_A04630361D3B524A');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_tax_rule');
        $this->addSql('DROP TABLE slack_notification');
        $this->addSql('DROP TABLE slack_webhook');
        $this->addSql('DROP TABLE tax_type');
        $this->addSql('DROP TABLE workflow_transition');
        $this->addSql('DROP TABLE workflow_transition_rule');
        $this->addSql('ALTER TABLE payment DROP country');
        $this->addSql('DROP INDEX IDX_D3D1D69384042C99');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type_id');
        $this->addSql('DROP INDEX IDX_3A4D412884042C99');
        $this->addSql('ALTER TABLE checkout_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line DROP tax_type_id');
        $this->addSql('ALTER TABLE charge_back_creation DROP has_error');
        $this->addSql('ALTER TABLE templates DROP locale');
        $this->addSql('DROP INDEX IDX_46316A7D84042C99');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_type_id');
        $this->addSql('ALTER TABLE cancellation_requests DROP cancellation_type');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('ALTER TABLE exchange_rates DROP original_currency');
        $this->addSql('CREATE UNIQUE INDEX uniq_5ae3e774fda273ec ON exchange_rates (currency_code)');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code)');
        $this->addSql('ALTER TABLE customers ADD tax_rate_digital DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_generator');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_tmp_dir');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_bin');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_api_key');
        $this->addSql('DROP INDEX IDX_D34A04AD84042C99');
        $this->addSql('ALTER TABLE product ADD tax_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type_id');
        $this->addSql('ALTER TABLE product DROP physical');
        $this->addSql('ALTER TABLE receipt ALTER vat_percentage SET NOT NULL');
        $this->addSql('DROP INDEX IDX_476F8F7A84042C99');
        $this->addSql('ALTER TABLE receipt_line DROP tax_type_id');
        $this->addSql('ALTER TABLE receipt_line DROP tax_country');
        $this->addSql('ALTER TABLE receipt_line DROP reverse_charge');
        $this->addSql('ALTER TABLE receipt_line ALTER vat_percentage SET NOT NULL');
        $this->addSql('DROP INDEX IDX_43F3EB7C84042C99');
        $this->addSql('ALTER TABLE quote_line ADD tax_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line DROP tax_type_id');
    }
}
