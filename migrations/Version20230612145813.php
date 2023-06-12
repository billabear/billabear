<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
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
final class Version20230612145813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stats_customer_creation_daily (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F057DF77BB8273378EB61006E5A02990 ON stats_customer_creation_daily (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_customer_creation_daily.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_customer_creation_monthly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42A1616BBB8273378EB61006E5A02990 ON stats_customer_creation_monthly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_customer_creation_monthly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE stats_customer_creation_yearly (id UUID NOT NULL, count INT NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B3B6251BB8273378EB61006E5A02990 ON stats_customer_creation_yearly (year, month, day)');
        $this->addSql('COMMENT ON COLUMN stats_customer_creation_yearly.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customers ALTER created_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE stats_customer_creation_daily');
        $this->addSql('DROP TABLE stats_customer_creation_monthly');
        $this->addSql('DROP TABLE stats_customer_creation_yearly');
        $this->addSql('ALTER TABLE customers ALTER created_at DROP NOT NULL');
    }
}
