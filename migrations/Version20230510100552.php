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
final class Version20230510100552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription_monthly_stats (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_monthly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_yearly_stats (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription_yearly_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_creation ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE subscription_monthly_stats');
        $this->addSql('DROP TABLE subscription_yearly_stats');
        $this->addSql('ALTER TABLE subscription_creation ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }
}