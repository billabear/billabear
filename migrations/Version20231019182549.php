<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019182549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 2023.04.02';
    }

    public function up(Schema $schema): void
    {
        // Charge Back Creation Workflow
        $this->addSql('ALTER TABLE charge_back_creation ADD has_error BOOLEAN DEFAULT NULL');
        $this->addSql('update charge_back_creation set has_error=false where state=\'completed\';');
        $this->addSql('update charge_back_creation set has_error=true where state!=\'completed\';');

        // Cancellation type
        $this->addSql('ALTER TABLE cancellation_requests ADD cancellation_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('update cancellation_requests set cancellation_type=\'company_request\'');

        // PDF Generator Settings
        $this->addSql('ALTER TABLE settings ADD system_settings_pdf_generator VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_pdf_tmp_dir VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_pdf_bin VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD system_settings_pdf_api_key VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE charge_back_creation DROP has_error');
        $this->addSql('ALTER TABLE cancellation_requests DROP cancellation_type');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_generator');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_tmp_dir');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_bin');
        $this->addSql('ALTER TABLE settings DROP system_settings_pdf_api_key');
    }
}
