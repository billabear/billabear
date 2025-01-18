<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118100856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE country ADD transaction_threshold INT DEFAULT NULL');
        $this->addSql('ALTER TABLE country ADD threshold_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE credit ADD accounting_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD accounting_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD customer_support_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD marketing_opt_in BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD newsletter_marketing_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD newsletter_announcement_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE expiring_card_process ALTER error TYPE VARCHAR(9999)');
        $this->addSql('ALTER TABLE invoice ADD converted_amount_due INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD converted_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD converted_sub_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD converted_tax_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD accounting_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD subscription_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD converted_net_price INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD converted_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD converted_sub_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_line ADD converted_tax_total INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice_line.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6939A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3D1D6939A1887DC ON invoice_line (subscription_id)');
        $this->addSql('ALTER TABLE invoice_process ALTER error TYPE VARCHAR(9999)');
        $this->addSql('ALTER TABLE payment ADD accounting_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD converted_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD converted_currency VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE process_trial_converted ALTER error TYPE VARCHAR(9999)');
        $this->addSql('ALTER TABLE process_trial_ended ALTER error TYPE VARCHAR(9999)');
        $this->addSql('ALTER TABLE process_trial_started ALTER error TYPE VARCHAR(9999)');
        $this->addSql('ALTER TABLE receipt ADD payment_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt ADD converted_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt ADD converted_sub_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt ADD converted_tax_total INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN receipt.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B6454C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5399B6454C3A3BB ON receipt (payment_id)');
        $this->addSql('ALTER TABLE receipt_line ADD subscription_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD converted_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD converted_sub_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt_line ADD converted_vat_total INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN receipt_line.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receipt_line ADD CONSTRAINT FK_476F8F7A9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_476F8F7A9A1887DC ON receipt_line (subscription_id)');
        $this->addSql('ALTER TABLE refund ADD accounting_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_integration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_oauth_settings_state_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_oauth_settings_access_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_oauth_settings_refresh_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD accounting_integration_oauth_settings_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_integration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_oauth_settings_state_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_oauth_settings_access_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_oauth_settings_refresh_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD customer_support_integration_oauth_settings_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_integration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_marketing_list_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_announcement_list_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_oauth_settings_state_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_oauth_settings_access_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_oauth_settings_refresh_token VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD newsletter_integration_oauth_settings_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE state ADD transaction_threshold INT DEFAULT NULL');
        $this->addSql('ALTER TABLE state ADD threshold_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE vouchers_application ADD accounting_reference VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vouchers_application DROP accounting_reference');
        $this->addSql('ALTER TABLE country DROP transaction_threshold');
        $this->addSql('ALTER TABLE country DROP threshold_type');
        $this->addSql('ALTER TABLE credit DROP accounting_reference');
        $this->addSql('ALTER TABLE invoice_process ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_enabled');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_integration');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_settings');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_oauth_settings_state_secret');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_oauth_settings_access_token');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_oauth_settings_refresh_token');
        $this->addSql('ALTER TABLE settings DROP accounting_integration_oauth_settings_expires_at');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_enabled');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_integration');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_settings');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_oauth_settings_state_secret');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_oauth_settings_access_token');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_oauth_settings_refresh_token');
        $this->addSql('ALTER TABLE settings DROP customer_support_integration_oauth_settings_expires_at');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_enabled');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_integration');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_marketing_list_id');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_announcement_list_id');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_settings');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_oauth_settings_state_secret');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_oauth_settings_access_token');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_oauth_settings_refresh_token');
        $this->addSql('ALTER TABLE settings DROP newsletter_integration_oauth_settings_expires_at');
        $this->addSql('ALTER TABLE refund DROP accounting_reference');
        $this->addSql('ALTER TABLE receipt_line DROP CONSTRAINT FK_476F8F7A9A1887DC');
        $this->addSql('DROP INDEX IDX_476F8F7A9A1887DC');
        $this->addSql('ALTER TABLE receipt_line DROP subscription_id');
        $this->addSql('ALTER TABLE receipt_line DROP converted_total');
        $this->addSql('ALTER TABLE receipt_line DROP converted_sub_total');
        $this->addSql('ALTER TABLE receipt_line DROP converted_vat_total');
        $this->addSql('ALTER TABLE state DROP transaction_threshold');
        $this->addSql('ALTER TABLE state DROP threshold_type');
        $this->addSql('ALTER TABLE customers DROP accounting_reference');
        $this->addSql('ALTER TABLE customers DROP customer_support_reference');
        $this->addSql('ALTER TABLE customers DROP marketing_opt_in');
        $this->addSql('ALTER TABLE customers DROP newsletter_marketing_reference');
        $this->addSql('ALTER TABLE customers DROP newsletter_announcement_reference');
        $this->addSql('ALTER TABLE receipt DROP CONSTRAINT FK_5399B6454C3A3BB');
        $this->addSql('DROP INDEX IDX_5399B6454C3A3BB');
        $this->addSql('ALTER TABLE receipt DROP payment_id');
        $this->addSql('ALTER TABLE receipt DROP converted_total');
        $this->addSql('ALTER TABLE receipt DROP converted_sub_total');
        $this->addSql('ALTER TABLE receipt DROP converted_tax_total');
        $this->addSql('ALTER TABLE invoice DROP converted_amount_due');
        $this->addSql('ALTER TABLE invoice DROP converted_total');
        $this->addSql('ALTER TABLE invoice DROP converted_sub_total');
        $this->addSql('ALTER TABLE invoice DROP converted_tax_total');
        $this->addSql('ALTER TABLE invoice DROP accounting_reference');
        $this->addSql('ALTER TABLE process_trial_ended ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment DROP accounting_reference');
        $this->addSql('ALTER TABLE payment DROP converted_amount');
        $this->addSql('ALTER TABLE payment DROP converted_currency');
        $this->addSql('ALTER TABLE process_trial_started ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE process_trial_converted ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE expiring_card_process ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D6939A1887DC');
        $this->addSql('DROP INDEX IDX_D3D1D6939A1887DC');
        $this->addSql('ALTER TABLE invoice_line DROP subscription_id');
        $this->addSql('ALTER TABLE invoice_line DROP converted_net_price');
        $this->addSql('ALTER TABLE invoice_line DROP converted_total');
        $this->addSql('ALTER TABLE invoice_line DROP converted_sub_total');
        $this->addSql('ALTER TABLE invoice_line DROP converted_tax_total');
    }
}
