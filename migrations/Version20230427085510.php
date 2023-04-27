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
final class Version20230427085510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand_settings (id UUID NOT NULL, code VARCHAR(255) NOT NULL, brand_name VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, is_default BOOLEAN NOT NULL, address_company_name VARCHAR(255) DEFAULT NULL, address_street_line_one VARCHAR(255) DEFAULT NULL, address_street_line_two VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_region VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, address_postcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN brand_settings.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE brand_settings');
    }
}
