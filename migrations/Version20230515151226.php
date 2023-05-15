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
final class Version20230515151226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_subscription_creation BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_expiring_card_warning BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE brand_settings ADD notification_settings_expiring_card_day_before BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE expiring_card_process ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE expiring_card_process ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE expiring_card_process ALTER subscription_charged_at TYPE TIMESTAMP(0) WITH TIME ZONE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_subscription_creation');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_expiring_card_warning');
        $this->addSql('ALTER TABLE brand_settings DROP notification_settings_expiring_card_day_before');
        $this->addSql('ALTER TABLE expiring_card_process ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE expiring_card_process ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE expiring_card_process ALTER subscription_charged_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }
}
