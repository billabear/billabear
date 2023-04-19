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
final class Version20230328093900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD payment_provider_details_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE price ADD public BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE price ADD payment_provider_details_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD payment_provider_details_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment DROP payment_provider_details_url');
        $this->addSql('ALTER TABLE price DROP public');
        $this->addSql('ALTER TABLE price DROP payment_provider_details_url');
        $this->addSql('ALTER TABLE product DROP payment_provider_details_url');
    }
}
