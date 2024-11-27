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
final class Version20241127231152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, customer_id UUID DEFAULT NULL, subscription_id UUID DEFAULT NULL, metric_id UUID DEFAULT NULL, event_id VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, properties JSON NOT NULL, PRIMARY KEY(id, created_at))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA79395C3F3 ON event (customer_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA79A1887DC ON event (subscription_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7A952D583 ON event (metric_id)');
        $this->addSql('COMMENT ON COLUMN event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event.metric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoice_delivery (id UUID NOT NULL, invoice_id UUID DEFAULT NULL, invoice_delivery_settings_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_28A9B6482989F1FD ON invoice_delivery (invoice_id)');
        $this->addSql('CREATE INDEX IDX_28A9B648EA1C5E18 ON invoice_delivery (invoice_delivery_settings_id)');
        $this->addSql('CREATE INDEX IDX_28A9B6489395C3F3 ON invoice_delivery (customer_id)');
        $this->addSql('COMMENT ON COLUMN invoice_delivery.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_delivery.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_delivery.invoice_delivery_settings_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_delivery.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoice_delivery_settings (id UUID NOT NULL, customer_id UUID DEFAULT NULL, enabled VARCHAR(255) NOT NULL, invoice_format VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, webhook_method VARCHAR(255) DEFAULT NULL, webhook_url VARCHAR(255) DEFAULT NULL, sftp_host VARCHAR(255) DEFAULT NULL, sftp_user VARCHAR(255) DEFAULT NULL, sftp_password VARCHAR(255) DEFAULT NULL, sftp_dir VARCHAR(255) DEFAULT NULL, sftp_port INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_879A8A7C9395C3F3 ON invoice_delivery_settings (customer_id)');
        $this->addSql('COMMENT ON COLUMN invoice_delivery_settings.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_delivery_settings.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE invoice_metric_counter (id UUID NOT NULL, metric_id UUID DEFAULT NULL, invoice_id UUID DEFAULT NULL, metric_counter_id UUID DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_94B89AA952D583 ON invoice_metric_counter (metric_id)');
        $this->addSql('CREATE INDEX IDX_94B89A2989F1FD ON invoice_metric_counter (invoice_id)');
        $this->addSql('CREATE INDEX IDX_94B89A4E05402E ON invoice_metric_counter (metric_counter_id)');
        $this->addSql('COMMENT ON COLUMN invoice_metric_counter.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_metric_counter.metric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_metric_counter.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice_metric_counter.metric_counter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE metric (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, aggregation_method VARCHAR(255) NOT NULL, aggregation_property VARCHAR(255) DEFAULT NULL, event_ingestion VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87D62EE377153098 ON metric (code)');
        $this->addSql('COMMENT ON COLUMN metric.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE metric_counter (id UUID NOT NULL, metric_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, value DOUBLE PRECISION NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E01686F6A952D583 ON metric_counter (metric_id)');
        $this->addSql('CREATE INDEX IDX_E01686F69395C3F3 ON metric_counter (customer_id)');
        $this->addSql('COMMENT ON COLUMN metric_counter.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN metric_counter.metric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN metric_counter.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE metric_filter (id UUID NOT NULL, metric_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF226429A952D583 ON metric_filter (metric_id)');
        $this->addSql('COMMENT ON COLUMN metric_filter.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN metric_filter.metric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE price_tier_component (id UUID NOT NULL, price_id UUID DEFAULT NULL, first_unit INT NOT NULL, last_unit INT DEFAULT NULL, unit_price INT NOT NULL, flat_fee INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E121B885D614C7E7 ON price_tier_component (price_id)');
        $this->addSql('COMMENT ON COLUMN price_tier_component.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN price_tier_component.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A952D583 FOREIGN KEY (metric_id) REFERENCES metric (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_delivery ADD CONSTRAINT FK_28A9B6482989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_delivery ADD CONSTRAINT FK_28A9B648EA1C5E18 FOREIGN KEY (invoice_delivery_settings_id) REFERENCES invoice_delivery_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_delivery ADD CONSTRAINT FK_28A9B6489395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_delivery_settings ADD CONSTRAINT FK_879A8A7C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_metric_counter ADD CONSTRAINT FK_94B89AA952D583 FOREIGN KEY (metric_id) REFERENCES metric (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_metric_counter ADD CONSTRAINT FK_94B89A2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_metric_counter ADD CONSTRAINT FK_94B89A4E05402E FOREIGN KEY (metric_counter_id) REFERENCES metric_counter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metric_counter ADD CONSTRAINT FK_E01686F6A952D583 FOREIGN KEY (metric_id) REFERENCES metric (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metric_counter ADD CONSTRAINT FK_E01686F69395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metric_filter ADD CONSTRAINT FK_FF226429A952D583 FOREIGN KEY (metric_id) REFERENCES metric (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE price_tier_component ADD CONSTRAINT FK_E121B885D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('create extension if not exists timescaledb;');
        $this->addSql('SELECT create_hypertable(\'event\', by_range(\'created_at\'));');
        $this->addSql('ALTER TABLE brand_settings ADD support_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD support_phone_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_payment_failure BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD invoice_format VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD invoiced_metric_counter_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice.invoiced_metric_counter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744EB2BB86C FOREIGN KEY (invoiced_metric_counter_id) REFERENCES invoice_metric_counter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_90651744EB2BB86C ON invoice (invoiced_metric_counter_id)');
        $this->addSql('ALTER TABLE invoice_line ADD product_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD net_price INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE invoice_line ADD quantity DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice_line.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6934584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3D1D6934584665A ON invoice_line (product_id)');
        $this->addSql('ALTER TABLE payment ADD payment_card_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN payment.payment_card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D538594CA FOREIGN KEY (payment_card_id) REFERENCES payment_card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D28840D538594CA ON payment (payment_card_id)');
        $this->addSql('ALTER TABLE price ADD metric_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE price ADD usage BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE price ADD units INT DEFAULT NULL');
        $this->addSql('ALTER TABLE price ADD type VARCHAR(255) NOT NULL DEFAULT \'fixed_price\'');
        $this->addSql('ALTER TABLE price ADD metric_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE price ALTER amount DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN price.metric_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D9A952D583 FOREIGN KEY (metric_id) REFERENCES metric (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CAC822D9A952D583 ON price (metric_id)');
        $this->addSql('ALTER TABLE settings ADD tax_settings_validate_tax_number BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD tax_settings_vat_sense_enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD tax_settings_vat_sense_api_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tax_type ADD vat_sense_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744EB2BB86C');
        $this->addSql('ALTER TABLE price DROP CONSTRAINT FK_CAC822D9A952D583');
        $this->addSql('create extension if not exists timescaledb;');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA79395C3F3');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA79A1887DC');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7A952D583');
        $this->addSql('ALTER TABLE invoice_delivery DROP CONSTRAINT FK_28A9B6482989F1FD');
        $this->addSql('ALTER TABLE invoice_delivery DROP CONSTRAINT FK_28A9B648EA1C5E18');
        $this->addSql('ALTER TABLE invoice_delivery DROP CONSTRAINT FK_28A9B6489395C3F3');
        $this->addSql('ALTER TABLE invoice_delivery_settings DROP CONSTRAINT FK_879A8A7C9395C3F3');
        $this->addSql('ALTER TABLE invoice_metric_counter DROP CONSTRAINT FK_94B89AA952D583');
        $this->addSql('ALTER TABLE invoice_metric_counter DROP CONSTRAINT FK_94B89A2989F1FD');
        $this->addSql('ALTER TABLE invoice_metric_counter DROP CONSTRAINT FK_94B89A4E05402E');
        $this->addSql('ALTER TABLE metric_counter DROP CONSTRAINT FK_E01686F6A952D583');
        $this->addSql('ALTER TABLE metric_counter DROP CONSTRAINT FK_E01686F69395C3F3');
        $this->addSql('ALTER TABLE metric_filter DROP CONSTRAINT FK_FF226429A952D583');
        $this->addSql('ALTER TABLE price_tier_component DROP CONSTRAINT FK_E121B885D614C7E7');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE invoice_delivery');
        $this->addSql('DROP TABLE invoice_delivery_settings');
        $this->addSql('DROP TABLE invoice_metric_counter');
        $this->addSql('DROP TABLE metric');
        $this->addSql('DROP TABLE metric_counter');
        $this->addSql('DROP TABLE metric_filter');
        $this->addSql('DROP TABLE price_tier_component');
        $this->addSql('ALTER TABLE tax_type DROP vat_sense_type');
        $this->addSql('ALTER TABLE customers DROP invoice_format');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D6934584665A');
        $this->addSql('DROP INDEX IDX_D3D1D6934584665A');
        $this->addSql('ALTER TABLE invoice_line DROP product_id');
        $this->addSql('ALTER TABLE invoice_line DROP net_price');
        $this->addSql('ALTER TABLE invoice_line DROP quantity');
        $this->addSql('DROP INDEX IDX_CAC822D9A952D583');
        $this->addSql('ALTER TABLE price DROP metric_id');
        $this->addSql('ALTER TABLE price DROP usage');
        $this->addSql('ALTER TABLE price DROP units');
        $this->addSql('ALTER TABLE price DROP type');
        $this->addSql('ALTER TABLE price DROP metric_type');
        $this->addSql('ALTER TABLE price ALTER amount SET NOT NULL');
        $this->addSql('ALTER TABLE settings DROP tax_settings_validate_tax_number');
        $this->addSql('ALTER TABLE settings DROP tax_settings_vat_sense_enabled');
        $this->addSql('ALTER TABLE settings DROP tax_settings_vat_sense_api_key');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D538594CA');
        $this->addSql('DROP INDEX IDX_6D28840D538594CA');
        $this->addSql('ALTER TABLE payment DROP payment_card_id');
        $this->addSql('ALTER TABLE brand_settings DROP support_email');
        $this->addSql('ALTER TABLE brand_settings DROP support_phone_number');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_payment_failure');
        $this->addSql('DROP INDEX IDX_90651744EB2BB86C');
        $this->addSql('ALTER TABLE invoice DROP invoiced_metric_counter_id');
    }
}
