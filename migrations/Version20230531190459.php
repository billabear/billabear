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
final class Version20230531190459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stats_cached_stats (id UUID NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value INT NOT NULL, currency VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68063FAB5E237E06 ON stats_cached_stats (name)');
        $this->addSql('COMMENT ON COLUMN stats_cached_stats.id IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP TABLE stats_mrr');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE stats_mrr (id UUID NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, year INT NOT NULL, month INT NOT NULL, day INT NOT NULL, date DATE NOT NULL, brand_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN stats_mrr.id IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP TABLE stats_cached_stats');
    }
}
