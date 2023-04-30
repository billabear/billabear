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
final class Version20230429210045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers ADD brand_settings_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN customers.brand_settings_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E2138C5B87D FOREIGN KEY (brand_settings_id) REFERENCES brand_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_62534E2138C5B87D ON customers (brand_settings_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customers DROP CONSTRAINT FK_62534E2138C5B87D');
        $this->addSql('DROP INDEX IDX_62534E2138C5B87D');
        $this->addSql('ALTER TABLE customers DROP brand_settings_id');
    }
}
