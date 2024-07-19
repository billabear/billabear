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
final class Version20240719093721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '2024.01.01';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(255) NOT NULL, iso_code VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, threshold BIGINT NOT NULL, start_of_tax_year VARCHAR(255) DEFAULT NULL, in_eu BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, collecting BOOLEAN DEFAULT NULL, tax_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN country.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE country_tax_rule (id UUID NOT NULL, country_id UUID DEFAULT NULL, tax_type_id UUID DEFAULT NULL, tax_rate DOUBLE PRECISION NOT NULL, is_default BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_699DF289F92F3E70 ON country_tax_rule (country_id)');
        $this->addSql('CREATE INDEX IDX_699DF28984042C99 ON country_tax_rule (tax_type_id)');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country_tax_rule.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE customer_subscription_events (id UUID NOT NULL, customer_id UUID DEFAULT NULL, subscription_id UUID DEFAULT NULL, done_by_id UUID DEFAULT NULL, event_type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A611B9619395C3F3 ON customer_subscription_events (customer_id)');
        $this->addSql('CREATE INDEX IDX_A611B9619A1887DC ON customer_subscription_events (subscription_id)');
        $this->addSql('CREATE INDEX IDX_A611B96135AE3EF9 ON customer_subscription_events (done_by_id)');
        $this->addSql('CREATE INDEX event_date_idx ON customer_subscription_events (event_type, created_at)');
        $this->addSql('COMMENT ON COLUMN customer_subscription_events.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_subscription_events.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_subscription_events.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_subscription_events.done_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE process_trial_ended (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, has_error BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_135A2C129A1887DC ON process_trial_ended (subscription_id)');
        $this->addSql('COMMENT ON COLUMN process_trial_ended.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN process_trial_ended.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE process_trial_extended (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, has_error BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27826F0F9A1887DC ON process_trial_extended (subscription_id)');
        $this->addSql('COMMENT ON COLUMN process_trial_extended.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN process_trial_extended.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE process_trial_started (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, error VARCHAR(255) DEFAULT NULL, has_error BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6EBEC199A1887DC ON process_trial_started (subscription_id)');
        $this->addSql('COMMENT ON COLUMN process_trial_started.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN process_trial_started.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE slack_notification (id UUID NOT NULL, slack_webhook_id UUID DEFAULT NULL, event VARCHAR(255) NOT NULL, message_template VARCHAR(10000) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FFC6C91B2A257EF ON slack_notification (slack_webhook_id)');
        $this->addSql('COMMENT ON COLUMN slack_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN slack_notification.slack_webhook_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE slack_webhook (id UUID NOT NULL, name VARCHAR(255) NOT NULL, webhook_url VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN slack_webhook.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE state (id UUID NOT NULL, country_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, threshold BIGINT NOT NULL, collecting BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A393D2FBF92F3E70 ON state (country_id)');
        $this->addSql('COMMENT ON COLUMN state.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE state_tax_rule (id UUID NOT NULL, state_id UUID DEFAULT NULL, tax_type_id UUID DEFAULT NULL, tax_rate DOUBLE PRECISION NOT NULL, is_default BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65A4C5935D83CC1 ON state_tax_rule (state_id)');
        $this->addSql('CREATE INDEX IDX_65A4C59384042C99 ON state_tax_rule (tax_type_id)');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.state_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state_tax_rule.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_ended_daily (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C7F0921BB8273378EB61006E5A02990 ON stats_trial_ended_daily (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_ended_daily.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_ended_monthly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1F06070BB8273378EB61006E5A02990 ON stats_trial_ended_monthly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_ended_monthly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_ended_yearly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19BFBE46BB8273378EB61006E5A02990 ON stats_trial_ended_yearly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_ended_yearly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_extended_daily (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3AF19AC4BB8273378EB61006E5A02990 ON stats_trial_extended_daily (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_extended_daily.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_extended_monthly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8BFFE3A9BB8273378EB61006E5A02990 ON stats_trial_extended_monthly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_extended_monthly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_extended_yearly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C9992622BB8273378EB61006E5A02990 ON stats_trial_extended_yearly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_extended_yearly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_started_daily (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8282B0B8BB8273378EB61006E5A02990 ON stats_trial_started_daily (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_started_daily.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_started_monthly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D3CEA4F6BB8273378EB61006E5A02990 ON stats_trial_started_monthly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_started_monthly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_trial_started_yearly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9092681FBB8273378EB61006E5A02990 ON stats_trial_started_yearly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_trial_started_yearly.id IS \'(DC2Type:uuid)\'');
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
        $this->addSql('ALTER TABLE customer_subscription_events ADD CONSTRAINT FK_A611B9619395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_subscription_events ADD CONSTRAINT FK_A611B9619A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_subscription_events ADD CONSTRAINT FK_A611B96135AE3EF9 FOREIGN KEY (done_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE process_trial_ended ADD CONSTRAINT FK_135A2C129A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE process_trial_extended ADD CONSTRAINT FK_27826F0F9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE process_trial_started ADD CONSTRAINT FK_E6EBEC199A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE slack_notification ADD CONSTRAINT FK_4FFC6C91B2A257EF FOREIGN KEY (slack_webhook_id) REFERENCES slack_webhook (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state_tax_rule ADD CONSTRAINT FK_65A4C5935D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE state_tax_rule ADD CONSTRAINT FK_65A4C59384042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workflow_transition_rule ADD CONSTRAINT FK_A04630361D3B524A FOREIGN KEY (workflow_transition_id) REFERENCES workflow_transition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_send_trial_ending_warning BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_send_before_charge_warnings VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_line RENAME COLUMN tax_type TO tax_state');
        $this->addSql('COMMENT ON COLUMN checkout_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D412884042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3A4D412884042C99 ON checkout_line (tax_type_id)');
        $this->addSql('ALTER TABLE checkout_session_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE checkout_session_line RENAME COLUMN tax_type TO tax_state');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7D84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_46316A7D84042C99 ON checkout_session_line (tax_type_id)');
        $this->addSql('ALTER TABLE customers DROP tax_rate_digital');
        $this->addSql('DROP INDEX uniq_5ae3e774fda273ec');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('DELETE FROM exchange_rates');
        $this->addSql('ALTER TABLE exchange_rates ADD original_currency VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code, original_currency)');
        $this->addSql('CREATE INDEX paid_idx ON invoice (paid)');
        $this->addSql('ALTER TABLE invoice_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN tax_type TO tax_state');
        $this->addSql('COMMENT ON COLUMN invoice_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D69384042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3D1D69384042C99 ON invoice_line (tax_type_id)');
        $this->addSql('ALTER TABLE payment ADD country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX threshold_idx ON payment (country, created_at)');
        $this->addSql('ALTER TABLE price ADD including_tax BOOLEAN DEFAULT false');
        $this->addSql('ALTER TABLE product ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD physical BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type');
        $this->addSql('COMMENT ON COLUMN product.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD84042C99 ON product (tax_type_id)');
        $this->addSql('ALTER TABLE quote_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_line RENAME COLUMN tax_type TO tax_state');
        $this->addSql('COMMENT ON COLUMN quote_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7C84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_43F3EB7C84042C99 ON quote_line (tax_type_id)');
        $this->addSql('ALTER TABLE receipt ALTER vat_percentage DROP NOT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_type_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD tax_state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD reverse_charge BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE receipt_line ALTER vat_percentage DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN receipt_line.tax_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receipt_line ADD CONSTRAINT FK_476F8F7A84042C99 FOREIGN KEY (tax_type_id) REFERENCES tax_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_476F8F7A84042C99 ON receipt_line (tax_type_id)');
        $this->addSql('ALTER TABLE settings ADD system_settings_invoice_number_format VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_stripe_public_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_stripe_private_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD tax_settings_one_stop_shop_tax_rules BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_plan ADD is_trial_standalone BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_plan RENAME COLUMN deleted TO is_deleted');
        $this->addSql('ALTER TABLE templates ADD locale VARCHAR(255) NOT NULL DEFAULT \'en\'');
        $this->addSql('ALTER TABLE users ADD locale VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D412884042C99');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7D84042C99');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D69384042C99');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD84042C99');
        $this->addSql('ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7C84042C99');
        $this->addSql('ALTER TABLE receipt_line DROP CONSTRAINT FK_476F8F7A84042C99');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF289F92F3E70');
        $this->addSql('ALTER TABLE country_tax_rule DROP CONSTRAINT FK_699DF28984042C99');
        $this->addSql('ALTER TABLE customer_subscription_events DROP CONSTRAINT FK_A611B9619395C3F3');
        $this->addSql('ALTER TABLE customer_subscription_events DROP CONSTRAINT FK_A611B9619A1887DC');
        $this->addSql('ALTER TABLE customer_subscription_events DROP CONSTRAINT FK_A611B96135AE3EF9');
        $this->addSql('ALTER TABLE process_trial_ended DROP CONSTRAINT FK_135A2C129A1887DC');
        $this->addSql('ALTER TABLE process_trial_extended DROP CONSTRAINT FK_27826F0F9A1887DC');
        $this->addSql('ALTER TABLE process_trial_started DROP CONSTRAINT FK_E6EBEC199A1887DC');
        $this->addSql('ALTER TABLE slack_notification DROP CONSTRAINT FK_4FFC6C91B2A257EF');
        $this->addSql('ALTER TABLE state DROP CONSTRAINT FK_A393D2FBF92F3E70');
        $this->addSql('ALTER TABLE state_tax_rule DROP CONSTRAINT FK_65A4C5935D83CC1');
        $this->addSql('ALTER TABLE state_tax_rule DROP CONSTRAINT FK_65A4C59384042C99');
        $this->addSql('ALTER TABLE workflow_transition_rule DROP CONSTRAINT FK_A04630361D3B524A');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_tax_rule');
        $this->addSql('DROP TABLE customer_subscription_events');
        $this->addSql('DROP TABLE process_trial_ended');
        $this->addSql('DROP TABLE process_trial_extended');
        $this->addSql('DROP TABLE process_trial_started');
        $this->addSql('DROP TABLE slack_notification');
        $this->addSql('DROP TABLE slack_webhook');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE state_tax_rule');
        $this->addSql('DROP TABLE stats_trial_ended_daily');
        $this->addSql('DROP TABLE stats_trial_ended_monthly');
        $this->addSql('DROP TABLE stats_trial_ended_yearly');
        $this->addSql('DROP TABLE stats_trial_extended_daily');
        $this->addSql('DROP TABLE stats_trial_extended_monthly');
        $this->addSql('DROP TABLE stats_trial_extended_yearly');
        $this->addSql('DROP TABLE stats_trial_started_daily');
        $this->addSql('DROP TABLE stats_trial_started_monthly');
        $this->addSql('DROP TABLE stats_trial_started_yearly');
        $this->addSql('DROP TABLE tax_type');
        $this->addSql('DROP TABLE workflow_transition');
        $this->addSql('DROP TABLE workflow_transition_rule');
        $this->addSql('ALTER TABLE users DROP locale');
        $this->addSql('DROP INDEX IDX_3A4D412884042C99');
        $this->addSql('ALTER TABLE checkout_line DROP tax_type_id');
        $this->addSql('ALTER TABLE checkout_line RENAME COLUMN tax_state TO tax_type');
        $this->addSql('ALTER TABLE templates DROP locale');
        $this->addSql('ALTER TABLE settings DROP system_settings_invoice_number_format');
        $this->addSql('ALTER TABLE settings DROP system_settings_stripe_public_key');
        $this->addSql('ALTER TABLE settings DROP system_settings_stripe_private_key');
        $this->addSql('ALTER TABLE settings DROP tax_settings_one_stop_shop_tax_rules');
        $this->addSql('DROP INDEX IDX_D3D1D69384042C99');
        $this->addSql('ALTER TABLE invoice_line DROP tax_type_id');
        $this->addSql('ALTER TABLE invoice_line RENAME COLUMN tax_state TO tax_type');
        $this->addSql('ALTER TABLE customers ADD tax_rate_digital DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_43F3EB7C84042C99');
        $this->addSql('ALTER TABLE quote_line DROP tax_type_id');
        $this->addSql('ALTER TABLE quote_line RENAME COLUMN tax_state TO tax_type');
        $this->addSql('ALTER TABLE subscription_plan DROP is_trial_standalone');
        $this->addSql('ALTER TABLE subscription_plan RENAME COLUMN is_deleted TO deleted');
        $this->addSql('DROP INDEX IDX_D34A04AD84042C99');
        $this->addSql('ALTER TABLE product ADD tax_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product DROP tax_type_id');
        $this->addSql('ALTER TABLE product DROP physical');
        $this->addSql('DROP INDEX IDX_476F8F7A84042C99');
        $this->addSql('ALTER TABLE receipt_line DROP tax_type_id');
        $this->addSql('ALTER TABLE receipt_line DROP tax_country');
        $this->addSql('ALTER TABLE receipt_line DROP tax_state');
        $this->addSql('ALTER TABLE receipt_line DROP reverse_charge');
        $this->addSql('ALTER TABLE receipt_line ALTER vat_percentage SET NOT NULL');
        $this->addSql('ALTER TABLE receipt ALTER vat_percentage SET NOT NULL');
        $this->addSql('ALTER TABLE price DROP including_tax');
        $this->addSql('DROP INDEX IDX_46316A7D84042C99');
        $this->addSql('ALTER TABLE checkout_session_line DROP tax_type_id');
        $this->addSql('ALTER TABLE checkout_session_line RENAME COLUMN tax_state TO tax_type');
        $this->addSql('DROP INDEX paid_idx');
        $this->addSql('DROP INDEX ex_rates_currency_code_idx');
        $this->addSql('ALTER TABLE exchange_rates DROP original_currency');
        $this->addSql('CREATE UNIQUE INDEX uniq_5ae3e774fda273ec ON exchange_rates (currency_code)');
        $this->addSql('CREATE INDEX ex_rates_currency_code_idx ON exchange_rates (currency_code)');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_send_trial_ending_warning');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_send_before_charge_warnings');
        $this->addSql('DROP INDEX threshold_idx');
        $this->addSql('ALTER TABLE payment DROP country');
        $this->addSql('ALTER TABLE payment DROP state');
    }
}
