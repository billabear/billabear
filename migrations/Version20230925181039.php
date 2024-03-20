<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925181039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 2023.04.01';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mass_subscription_change (id UUID NOT NULL, target_subscription_plan_id UUID DEFAULT NULL, new_subscription_plan_id UUID DEFAULT NULL, target_price_id UUID DEFAULT NULL, new_price_id UUID DEFAULT NULL, brand_settings_id UUID DEFAULT NULL, created_by_id UUID DEFAULT NULL, change_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, target_country VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A63C0CACF1B966A3 ON mass_subscription_change (target_subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_A63C0CAC6C4D9255 ON mass_subscription_change (new_subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_A63C0CACCBF178E ON mass_subscription_change (target_price_id)');
        $this->addSql('CREATE INDEX IDX_A63C0CAC917D4BCB ON mass_subscription_change (new_price_id)');
        $this->addSql('CREATE INDEX IDX_A63C0CAC38C5B87D ON mass_subscription_change (brand_settings_id)');
        $this->addSql('CREATE INDEX IDX_A63C0CACB03A8386 ON mass_subscription_change (created_by_id)');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.target_subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.new_subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.target_price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.new_price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.brand_settings_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mass_subscription_change.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CACF1B966A3 FOREIGN KEY (target_subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CAC6C4D9255 FOREIGN KEY (new_subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CACCBF178E FOREIGN KEY (target_price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CAC917D4BCB FOREIGN KEY (new_price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CAC38C5B87D FOREIGN KEY (brand_settings_id) REFERENCES brand_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mass_subscription_change ADD CONSTRAINT FK_A63C0CACB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE parthenon_export_background_export_requests (id UUID NOT NULL, exported_file VARCHAR(255) DEFAULT NULL, exported_file_path VARCHAR(255) DEFAULT NULL, export_format VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, data_provider_service VARCHAR(255) NOT NULL, data_provider_parameters JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN parthenon_export_background_export_requests.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cancellation_requests ADD has_error BOOLEAN DEFAULT NULL');
        $this->addSql('update cancellation_requests  set has_error=false where state=\'completed\';');
        $this->addSql('update cancellation_requests  set has_error=true where state!=\'completed\';');
        $this->addSql('ALTER TABLE cancellation_requests ALTER error TYPE TEXT');
        $this->addSql('ALTER TABLE subscription_creation ADD has_error BOOLEAN DEFAULT NULL');
        $this->addSql('update subscription_creation  set has_error=false where state=\'completed\';');
        $this->addSql('update subscription_creation  set has_error=true where state!=\'completed\';');
        $this->addSql('ALTER TABLE payment_creation ADD has_error BOOLEAN DEFAULT NULL');
        $this->addSql('update payment_creation  set has_error=false where state=\'completed\';');
        $this->addSql('update payment_creation  set has_error=true where state!=\'completed\';');
        $this->addSql('ALTER TABLE refund_created_process ADD has_error BOOLEAN DEFAULT NULL');
        $this->addSql('update refund_created_process  set has_error=false where state=\'completed\';');
        $this->addSql('update refund_created_process  set has_error=true where state!=\'completed\';');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CACF1B966A3');
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CAC6C4D9255');
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CACCBF178E');
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CAC917D4BCB');
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CAC38C5B87D');
        $this->addSql('ALTER TABLE mass_subscription_change DROP CONSTRAINT FK_A63C0CACB03A8386');
        $this->addSql('DROP TABLE mass_subscription_change');
        $this->addSql('DROP TABLE parthenon_export_background_export_requests');
        $this->addSql('ALTER TABLE cancellation_requests DROP has_error');
        $this->addSql('ALTER TABLE cancellation_requests ALTER error TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE subscription_creation DROP has_error');
        $this->addSql('ALTER TABLE payment_creation DROP has_error');
        $this->addSql('ALTER TABLE refund_created_process DROP has_error');
    }
}
